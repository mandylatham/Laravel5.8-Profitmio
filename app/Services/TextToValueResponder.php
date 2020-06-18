<?php

namespace App\Services;

use App\Models\Company;
use App\Models\PhoneNumber;
use App\Models\Recipient;
use App\Models\RecipientTextToValue;
use App\Models\Response;
use App\Models\TextToValueOptIn;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Twilio\TwiML\MessagingResponse;
use Cache;
use Log;

class TextToValueResponder
{
    const INVALID_OPT_IN_RESPONSE_MESSAGE = 'Invalid response, reply yes';

    public function __construct()
    {}

    public function generateMessageResponse($message, $toNumber, $fromNumber)
    {
        try {
            $phoneNumber = PhoneNumber::with('campaign')
                ->whereRaw("replace(phone_number, '+1', '') like '%{$toNumber}'")
                ->firstOrFail();

            $campaign = $phoneNumber->campaign;

            if ($this->waitingPendingOptInResponse($fromNumber)) {
                $recipient = $this->getRecipientFromPendingOptInResponse($fromNumber);

                if ($message === 'yes' || $message === 'Y') {
                    $optInMessage = new TextToValueOptIn();
                    $optInMessage->recipient_id = $recipient->id;
                    $optInMessage->phone_number = $fromNumber;
                    $optInMessage->accepted = true;
                    $optInMessage->save();

                    $this->clearPendingOptInResponseStatus($fromNumber);

                    $this->sendTextToValueToRecipient($recipient, $phoneNumber, $fromNumber, $message);

                    return response('<Response></Response>')->header('Content-Type', 'text/xml');
                } else {
                    $response = new MessagingResponse();
                    $response->message($this->getRequestOptInMessage($campaign->dealership, $phoneNumber));
                    return $response;
                }
            }

            $textToValue = RecipientTextToValue::select('recipient_text_to_value.id', 'recipient_text_to_value.recipient_id', 'recipient_text_to_value.text_to_value_code', 'recipient_text_to_value.text_to_value_amount')
                ->join('recipients', 'recipients.id', '=', 'recipient_text_to_value.recipient_id')
                ->where('recipients.campaign_id', $campaign->id)
                ->where('text_to_value_code', $message)
                ->first();

            if (!$textToValue || ($textToValue && !$textToValue->recipient)) {
                $response = new MessagingResponse();
                $response->message('Code not found');
                return $response;
            }

            $recipient = $textToValue->recipient;

            if (!$this->hasAcceptedTextToValueOptIn($fromNumber, $recipient)) {
                $this->setPendingOptInResponseStatus($fromNumber, $recipient);

                $response = new MessagingResponse();
                $response->message($this->getRequestOptInMessage($campaign->dealership, $phoneNumber));
                return $response;
            }

            $this->sendTextToValueToRecipient($recipient, $phoneNumber, $fromNumber, $message);

            return response('<Response></Response>')->header('Content-Type', 'text/xml');
        } catch (ModelNotFoundException $e) {
            Log::error("Model not found: " . $e->getMessage());
            return response('<Response><Reject /></Response>', 401)
                ->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            Log::error("Exception: " . $e->getMessage());
            return response("<Response>{$e->getMessage()}</Response>", 401)
                ->header('Content-Type', 'text/xml');
        }
    }

    public function getRecipientFromPendingOptInResponse($fromNumber)
    {
        return Recipient::findOrFail(Cache::get($this->getCacheKey($fromNumber, 'recipient-id')));
    }

    private function getRequestOptInMessage(Company $dealership, PhoneNumber $phoneNumber)
    {
        return "Reply Y to consent to rcv mktg msgs from $dealership->name at this $phoneNumber->phone. Msg&data rates may apply. Reply STOP=stop, HELP=help. [#]msgs/mo. No purchase req’d.";

//        if ($message === 'yes') {
//            $optInMessage = new TextToValueOptIn();
//            $optInMessage->recipient_id = $recipient->id;
//            $optInMessage->accepted = true;
//            $optInMessage->save();
//
//            $this->clearPendingOptInResponseStatus($recipient);
//
//        } else {
//            $response = new MessagingResponse();
//            $response->message("Reply Y to consent to rcv mktg msgs from $dealership->name at this $phoneNumber->phone. Msg&data rates may apply. Reply STOP=stop, HELP=help. [#]msgs/mo. No purchase req’d.");
//            return $response;
//        }

    }

    private function hasAcceptedTextToValueOptIn($fromNumber, Recipient $recipient)
    {
        return TextToValueOptIn::where('phone_number', $fromNumber)
                ->where('recipient_id', $recipient->id)
                ->where('accepted', true)
                ->count() > 0;
    }

    private function setPendingOptInResponseStatus($fromNumber, Recipient $recipient)
    {
        $expiresAt = now()->addMinutes(10);
        Cache::put($this->getCacheKey($fromNumber, 'recipient-id'), $recipient->id, $expiresAt);
        Cache::put($this->getCacheKey($fromNumber, 'waiting'), true, $expiresAt);
    }

    private function waitingPendingOptInResponse($fromNumber)
    {
        return Cache::get($this->getCacheKey($fromNumber, 'waiting'));
    }

    private function clearPendingOptInResponseStatus($fromNumber)
    {
        return Cache::forget($this->getCacheKey($fromNumber, 'waiting'));
    }

    private function getCacheKey($number, $id)
    {
        return 'ttv-opt-in' . $id . '.' . Str::slug($number);
    }

    private function isUnsubscribeMessage($message)
    {
        $message = $this->simplifySmsMessage($message);

        if ($this->containsUnsubscribeVerbage($message)) {
            return true;
        }

        return false;
    }

    private function containsUnsubscribeVerbage($message)
    {
        return in_array($message, ['stop', 'unsubscribe', 'stopall', 'cancel', 'end', 'quit']);
    }

    private function sendTextToValueToRecipient(Recipient $recipient, PhoneNumber $phoneNumber, $fromNumber, $message)
    {
        // @todo refactor this conditional which repeats in this class
        if ($recipient->status !== Recipient::NEW_STATUS &&
            $recipient->status !== Recipient::CLOSED_STATUS &&
            $recipient->status !== Recipient::OPEN_STATUS
        ) {
            $recipient->status = Recipient::NEW_STATUS;
            $recipient->last_status_changed_at = Carbon::now()->toDateTimeString();
        }

        if ($recipient->phone !== $fromNumber && $recipient->phone !== '+1'.$fromNumber) {
            $recipient->phone = $fromNumber;
        }

        $recipient->last_responded_at = \Carbon\Carbon::now('UTC');
        $recipient->responses()->create([
            'message' => $message,
            'incoming' => 1,
            'type' => Response::TTV_TYPE,
            'recording_sid' => 0,
            'campaign_id' => $recipient->campaign->id,
        ]);
        $recipient->save();

        // unsubscribe happens at twilio level
        if ($this->isUnsubscribeMessage($message)) {
            $suppress = new \App\Models\SmsSuppression([
                'phone' => substr($recipient->phone, -10, 10),
                'suppressed_at' => \Carbon\Carbon::now('UTC'),
            ]);
            $suppress->save();
        }

        $textToValue = $recipient->textToValue;
        $textToValue->value_requested = true;
        $textToValue->value_requested_at = Carbon::now('UTC')->toDateTimeString();
        $textToValue->save();

        $valueMessage = $recipient->campaign->getTextToValueMessageForRecipient($recipient);

        $twilioClient = new TwilioClient();
        $twilioClient->sendSms($phoneNumber->phone_number, $recipient->phone, $valueMessage);
        $twilioClient->sendSms($phoneNumber->phone_number, $recipient->phone, '', $recipient->qrCode->image_url);

        $recipient->responses()->create([
            'message' => $valueMessage,
            'incoming' => 0,
            'type' => Response::TTV_TYPE,
            'recording_sid' => 0,
            'campaign_id' => $recipient->campaign->id,
            'recipient_id' => $recipient->id,
        ]);

    }

    private function simplifySmsMessage($message)
    {
        return preg_replace('/[^A-Za-z0-9]*/', '', strtolower(trim($message)));
    }
}

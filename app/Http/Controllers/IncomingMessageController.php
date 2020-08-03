<?php

namespace App\Http\Controllers;

use App\Classes\MailgunService;
use App\Events\RecipientPhoneResponseReceived;
use App\Events\RecipientTextResponseReceived;
use App\Models\EmailLog;
use App\Models\TextToValueOptIn;
use App\Services\TextToValueResponder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Campaign;
use App\Models\PhoneNumber;
use App\Models\Response;
use App\Models\Recipient;
use App\Services\SentimentService;
use Illuminate\Http\Request;
use App\Events\CampaignCountsUpdated;
use App\Events\RecipientEmailResponseReceived;
use App\Services\TwilioClient;
use Twilio\TwiML\MessagingResponse;
use Illuminate\Log\Logger;
use Carbon\Carbon;
use Log;
use Cache;
use Twilio\TwiML\VoiceResponse;

class IncomingMessageController extends Controller
{
    private $sentiment;

    private $mailgun;

    private $log;

    private $textToValueResponder;

    public function __construct(SentimentService $sentiment, Logger $log, TextToValueResponder $textToValueResponder, MailgunService $mailgun)
    {
        $this->sentiment = $sentiment;
        $this->mailgun = $mailgun;
        $this->log = $log;
        $this->textToValueResponder = $textToValueResponder;
    }

    /**
     * Handle inbound email message from Mailgun
     *
     * @param Request $request The Mailgun inbound request
     */
    public function receiveEmailMessage(Request $request)
    {
        list($campaign_id, $recipient_id) = $this->getEmailMetadata($request->get('To'));

        $campaign = Campaign::findOrFail($campaign_id);
        $recipient = Recipient::findOrFail($recipient_id);

        $response = new Response([
            'campaign_id' => $campaign->id,
            'recipient_id' => $recipient->id,
            'message' => $request->get('stripped-text'),
            'message_id' => $request->get('Message-Id'),
            'in_reply_to' => $request->get('In-Reply-To'),
            'subject' => $request->get('subject'),
            'type' => 'email',
            'recording_sid' => 0,
            'incoming' => 1,
        ]);

        $response->save();

        if ($recipient->status !== Recipient::NEW_STATUS &&
            $recipient->status !== Recipient::CLOSED_STATUS &&
            $recipient->status !== Recipient::OPEN_STATUS
        ) {
            $recipient->status = Recipient::NEW_STATUS;
            $recipient->last_status_changed_at = Carbon::now()->toDateTimeString();
        }
        $recipient->last_responded_at = \Carbon\Carbon::now('UTC');
        $recipient->save();

        $this->sentiment->forResponse($response);

        event(new RecipientEmailResponseReceived($campaign, $recipient, $response));
        event(new CampaignCountsUpdated($campaign));

        if ($campaign->client_passthrough && !empty($campaign->client_passthrough_email)) {
            $this->mailgun->sendPassthroughEmail(
                $campaign,
                $recipient,
                $request->get('subject'),
                $request->get('stripped-html'),
                $request->get('stripped-text')
            );
        }
    }

    /**
     * Log email events from Mailgun
     *
     * @param \Illuminate\Http\Request $request
     */
    public function logEmail(Request $request)
    {
        //find out if we can grab the campaign details from the messageId
        $log = new EmailLog();
        $messageId = null;
        //somtimes message ID is a different variable.
        if ($request->has('Message-Id')) {
            $messageId = str_replace(['<', '>'], '', $request->input('Message-Id'));
        }

        if ($request->has('message-id')) {
            $messageId = $request->input('message-id');
        }

        if (!$messageId) {
            $this->log->error('Received bad request from Mailgun: (cannot find message-id) ' . json_encode($request->all(), JSON_UNESCAPED_SLASHES));
            abort(406);
        }

        $log->message_id = $messageId;

        $existing = EmailLog::where('message_id', $messageId)
            ->where('campaign_id', '!=', 0)
            ->orderBy('id', 'ASC')
            ->first();

        if ($existing) {
            $log->campaign_id = $existing->campaign_id;
            $log->recipient_id = $existing->recipient_id;
        } else {
            $campaign = null;
            $recipient = null;
            if ($request->has('tag') && $request->has('recipient') && $campaign = $this->getCampaignFromEmailTag($request)) {
                $recipient = $campaign->recipients()->whereEmail($request->input('recipient'))->first();
            }
            if (!$recipient) {
                $from = $this->parseMailgunFromField($request->input('from'));
                $log->campaign_id = $from->campaign_id;
                $log->recipient_id = $from->recipient_id;
                if (!$from->campaign_id || !$from->recipient_id) {
                    Log::error('Received bad request from Mailgun (cannot find campaign) ' . json_encode($request->all(), JSON_UNESCAPED_SLASHES));

                    abort(406);
                }
            } else {
                $log->campaign_id = $campaign->id;
                $log->recipient_id = $recipient->id;
            }
        }

        $log->code = $request->input('code') ?: '000';
        $log->event = $request->input('event');
        $log->recipient = $request->input('recipient');
        $log->save();
    }

    /**
     * Get the Campaign object from the email request
     *
     * @param Request $request The mailgun email request
     *
     * @return Campaign
     */
    private function getCampaignFromEmailTag($request)
    {
        $tag = $request->input('tag');
        if (is_array($tag)) {
            $id = str_replace('profitminer_campaign_', '', $tag[0]);
        } else {
            $id = str_replace('profitminer_campaign_', '', $tag);
        }
        return Campaign::find($id);
    }

    /**
     * Handle inbound sms message from Twilio
     *
     * @param Request $request The twilio inbound request
     */
    public function receiveSmsMessage(Request $request)
    {
        try {
            $phoneNumber = $this->getPhoneNumberFromRequest($request);

            if ($phoneNumber->isMailer()) {
                if ($phoneNumber->campaign->enable_text_to_value) {
                    $message = preg_replace('/[^\w\s]*/', '', $request->get('Body'));
                    $toNumber = str_replace('+1', '', trim($request->input('To') ?: $request->input('Called')));
                    $fromNumber = str_replace('+1', '', trim($request->input('From')));
                    return $this->textToValueResponder->generateMessageResponse($message, $toNumber, $fromNumber);
                }

                Log::error("Received SMS on non-Text-To-Value Mailer number ({$phoneNumber})");
                return response('<Response><Reject /></Response>', 401)
                    ->header('Content-Type', 'text/xml');
            }

            return $this->processSmsMessage($request);
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

    public function processSmsMessage(Request $request)
    {
        try {
            list($phoneNumber, $campaign, $recipient) = $this->getRequestObjects($request);

            $invalidCharacters = '/[^\w\s]*/';
            $message = preg_replace($invalidCharacters, '', $request->get('Body'));

            $response = new Response([
                'message' => $message,
                'incoming' => 1,
                'type' => 'text',
                'recording_sid' => 0,
                'campaign_id' => $campaign->id,
            ]);

            if (!$recipient) {
                # Lookup caller's "caller-name" from Twilio
                $recipient = $this->createRecipientFromSender($request, $campaign);

                if ($campaign->hasPassthrough) {
                    $this->mailgun->sendClientEmail(
                        $campaign,
                        $recipient,
                        '(ProfitMiner.io SMS) Campaign: ' . $campaign->name . '/' . $recipient->name . '/' . $recipient->phone,
                        null,
                        $message
                    );
                }
            }

            if ($recipient->status !== Recipient::NEW_STATUS &&
                $recipient->status !== Recipient::CLOSED_STATUS &&
                $recipient->status !== Recipient::OPEN_STATUS
            ) {
                $recipient->status = Recipient::NEW_STATUS;
                $recipient->last_status_changed_at = Carbon::now()->toDateTimeString();
            }

            $recipient->last_responded_at = \Carbon\Carbon::now('UTC');
            $recipient->save();

            $response->recipient_id = $recipient->id;
            $response->save();

            $this->sentiment->forResponse($response);

            event(new RecipientTextResponseReceived($response));
            event(new CampaignCountsUpdated($campaign));

            // ubsubscribe happens at twilio level
            if ($this->isUnsubscribeMessage($message)) {
                $suppress = new \App\Models\SmsSuppression([
                    'phone' => substr($recipient->phone, -10, 10),
                    'suppressed_at' => \Carbon\Carbon::now('UTC'),
                ]);
                $suppress->save();
            }

            if ($phoneNumber->forward) {
                return response('<Response><Message to="' . $phoneNumber->forward . '">' . htmlspecialchars(substr($request->input('from') . ': ' . $request->input('body'), 0, 1600)) . ' </Message></Response>')->header('Content-Type', 'text/xml');
            }
            return response('<Response></Response>')->header('Content-Type', 'text/xml');
        } catch (ModelNotFoundException $e) {
            Log::error("Model not found: " . $e->getMessage());
            return response('<Response><Reject /></Response>', 401)->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            Log::error("Exception: " . $e->getMessage());
            return response("<Response>{$e->getMessage()}</Response>", 401)->header('Content-Type', 'text/xml');
        }
    }

    public function processTextToValueMessage(Request $request)
    {
        $invalidCharacters = '/[^\w\s]*/';
        $message = preg_replace($invalidCharacters, '', $request->get('Body'));
        try {
            $number = str_replace('+1', '', trim($request->input('To') ?: $request->input('Called')));
            $fromNumber = str_replace('+1', '', trim($request->input('From')));

            $phoneNumber = PhoneNumber::with('campaign')
                ->whereRaw("replace(phone_number, '+1', '') like '%{$number}'")
                ->firstOrFail();

            $campaign = $phoneNumber->campaign;

            $recipient = $phoneNumber->campaign->recipients()
                ->whereRaw("replace(phone, '+1', '') = ?", [$fromNumber])
                ->first();

            if (!$recipient) {
                $response = new MessagingResponse();
                $response->message('Invalid code');
                return $response;
            }

            $pendingOptIn = Cache::get('recipient.' . $recipient->id . '.waiting-opt-message');
            if ($pendingOptIn) {
                if ($message === 'yes') {
                    $optInMessage = new TextToValueOptIn();
                    $optInMessage->recipient_id = $recipient->id;
                    $optInMessage->accepted = true;
                    $optInMessage->save();

                    Cache::forget('recipient.' . $recipient->id . '.waiting-opt-message');

                    $response = new MessagingResponse();
                    $response->message('Respond with the code');
                    return $response;
                } else {
                    $response = new MessagingResponse();
                    $response->message('Invalid code');
                    return $response;
                }
            }

            if (!$recipient->hasAcceptedTextToValueMessages()) {
                $expiresAt = now()->addMinutes(10);
                Cache::put('recipient.' . $recipient->id . '.waiting-opt-message', true, $expiresAt);

                $response = new MessagingResponse();
                $response->message('Reply yes');
                return $response;
            }

            $recipient = $campaign->recipients()
                ->wherehas('textToValue', function ($subQ) use ($message) {
                    $subQ->whereTextToValueCode($message);
                })
                ->with('textToValue')
                ->first();

            if (! $recipient) {
                $valueMessage = 'Code not found';
                $twilioResponse = new MessagingResponse();
                $twilioMessage = $twilioResponse->message('');
                $twilioMessage->body($valueMessage);

                return $twilioResponse;
            }

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
                'campaign_id' => $campaign->id,
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

            if ($recipient->textToValue && $recipient->textToValue->text_to_value_code === $message) {
                $textToValue = $recipient->textToValue;
                $textToValue->value_requested = true;
                $textToValue->value_requested_at = Carbon::now('UTC')->toDateTimeString();
                $textToValue->save();

                $valueMessage = $campaign->getTextToValueMessageForRecipient($recipient);

                $twilioClient = new TwilioClient();
                $twilioClient->sendSms($phoneNumber->phone_number, $recipient->phone, $valueMessage);
                $twilioClient->sendSms($phoneNumber->phone_number, $recipient->phone, '', $recipient->qrCode->image_url);

                $recipient->responses()->create([
                    'message' => $valueMessage,
                    'incoming' => 0,
                    'type' => Response::TTV_TYPE,
                    'recording_sid' => 0,
                    'campaign_id' => $campaign->id,
                    'recipient_id' => $recipient->id,
                ]);

                return response('<Response></Response>')->header('Content-Type', 'text/xml');
            }
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

    /**
     * Handle inbound phone call from Twilio
     *
     * @param Request $request The twilio inbound request
     */
    public function receivePhoneCall(Request $request)
    {
        try {
            list($phoneNumber, $campaign, $recipient) = $this->getRequestObjects($request);

            $response = Response::where('call_sid', $request->get('CallSid'))->first();

            $calling_to = PhoneNumber::wherePhoneNumber($request->get('To'))->first();
            $phone_number_id = null;
            if ($calling_to) {
                $phone_number_id = $calling_to->id;
            }

            if (!$response) {
                $response = new Response([
                    'call_sid' => $request->get('CallSid'),
                    'call_phone_number_id' => $phone_number_id,
                    'incoming' => 1,
                    'type' => 'phone',
                    'duration' => $request->get('CallDuration') ?: 0,
                    'campaign_id' => $campaign->id,
                    'response_source' => $request->get('From'),
                    'response_destination' => $request->get('To'),
                ]);
            }

            if (!$recipient) {
                $recipient = $this->createRecipientFromSender($request, $campaign);
            }

            $response->recipient_id = $recipient->id;
            $response->save();

            $recipient->last_responded_at = \Carbon\Carbon::now('UTC');
            $recipient->save();

            event(new RecipientPhoneResponseReceived($campaign, $recipient, $response));
            event(new CampaignCountsUpdated($campaign));

            $forwardNumber = $phoneNumber->forward;
            if ($campaign->enable_call_center && $campaign->cloud_one_phone_number) {
                $forwardNumber = $campaign->cloud_one_phone_number;
            }

            $response = new VoiceResponse();
            if (!$forwardNumber) {
                $response->say("There was an error connecting your call, please try again later.", ['voice' => 'polly.joanna']);
                return $response;
            }
            if (!$campaign->enable_call_center) {
                $response->say("This call may be recorded for quality assurance purposes", ['voice' => 'Polly.Joanna']);
            }
            $response->dial($forwardNumber, ['record' => 'record-from-answer']);
            return $response;
        } catch (\Exception $e) {
            Log::error("inboundPhone(): {$e->getMessage()}");

            return response('<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
                '<Response><Reject>' . $e->getMessage() . '</Reject></Response>', 401)
                ->header('Content-Type', 'text/xml');
        }
    }

    public function receivePhoneCallStatus(Request $request)
    {
        $recording = (new TwilioClient)->getRecordingFromSid($request->input('CallSid'));
        if (empty($recording)) {
            return response('<Response>No recordings found, none processed</Response>')
                ->header('Content-Type', 'text/xml');
        }

        $response = Response::where('call_sid', $request->input('CallSid'))->firstOrFail();

        $response->duration = $request->input('CallDuration');
        $response->recording_uri = $recording->uri;
        $response->recording_sid = $recording->sid;

        $response->save();

        return response('<Response>Recording processed for response id ' . $response->id . '</Response>')
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Parse out the encoded email data
     *
     * @param $email
     *
     * @return array
     */
    protected function getEmailMetadata($email): array
    {
        $metadata = preg_split('/(_|@)/', $email);

        $campaign_id = $metadata[1];
        $recipient_id = $metadata[2];

        return [$campaign_id, $recipient_id];
    }

    /**
     * Get the objects from the Request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function getRequestObjects(Request $request)
    {
        if ((!$request->has('To') && !$request->has('Called')) || !$request->has('From')) {
            throw new \Exception("A critical phone component is missing from the request: " . json_encode($request->all()));
        }

        $number = str_replace('+1', '', trim($request->input('To') ?: $request->input('Called')));
        $fromNumber = str_replace('+1', '', trim($request->input('From')));


        $phoneNumber = PhoneNumber::with('campaign')
            ->whereRaw("replace(phone_number, '+1', '') like '%{$number}'")
            ->firstOrFail();

        $recipient = $phoneNumber->campaign->recipients()
            ->whereRaw("replace(phone, '+1', '') = ?", [$fromNumber])
            ->first();

        return [$phoneNumber, $phoneNumber->campaign, $recipient];
    }

    protected function getPhoneNumberFromRequest(Request $request)
    {
        $number = str_replace('+1', '', trim($request->input('To') ?: $request->input('Called')));
        $phoneNumber = PhoneNumber::with('campaign')
            ->whereRaw("replace(phone_number, '+1', '') like '%{$number}'")
            ->firstOrFail();
        return $phoneNumber;
    }

    /**
     * Create a Recipient from a Twilio request
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $campaign
     *
     * @return \App\Models\Recipient
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    protected function createRecipientFromSender(Request $request, $campaign)
    {
        // Lookup caller's "caller-name" from Twilio
        // TODO: resolve unverified call output
        $sender = (object)(new TwilioClient)->getNameFromPhoneNumber($request->get('From'));

        $data = [
            'first_name' => $sender->first_name,
            'last_name' => $sender->last_name,
            'phone' => $request->get('From'),
            'campaign_id' => $campaign->id,
        ];
        $recipient = $campaign->findOrCreateRecipientByPhone($request->get('From'), $data);

        return $recipient;
    }

    /**
     * Parse out From field from Mailgun message
     *
     * @param $from
     *
     * @return mixed
     */
    private function parseMailgunFromField($from)
    {
        $data = new class() {
            public function __get($name)
            {
                if (!isset($this->name)) {
                    return null;
                }

                return $this->name;
            }
        };

        if (strpos($from, '<') && strpos($from, '@')) {
            $email_pieces = explode('<', $from);
            $email = str_replace('>', '', $email_pieces[1]);
            $email_name = substr($email, 0, strpos($email, '@'));
            $name_pieces = explode('_', $email_name);
            if (count($name_pieces) == 3) {
                $data->campaign_id = $name_pieces[1];
                $data->recipient_id = $name_pieces[2];
            }
        }

        return $data;
    }

    /**
     * Check for Unsubscribe Verbage
     *
     * @param $message
     *
     * @return bool
     */
    private function isUnsubscribeMessage($message)
    {
        $message = $this->simplifySmsMessage($message);

        if ($this->containsUnsubscribeVerbage($message)) {
            return true;
        }

        return false;
    }

    /**
     * Contains Unsubscribe Verbage
     *
     * @param $message
     *
     * @return bool
     */
    private function containsUnsubscribeVerbage($message)
    {
        return in_array($message, ['stop', 'unsubscribe', 'stopall', 'cancel', 'end', 'quit']);
    }

    /**
     * Simplify the Sms Message Body
     *
     * @param $message
     *
     * @return string|string[]|null
     */
    private function simplifySmsMessage($message)
    {
        return preg_replace('/[^A-Za-z0-9]*/', '', strtolower(trim($message)));
    }
}

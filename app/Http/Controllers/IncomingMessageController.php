<?php

namespace App\Http\Controllers;

use App\Classes\MailgunService;
use App\Events\RecipientTextResponseReceived;
use App\Models\EmailLog;
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
use Illuminate\Log\Logger;
use Log;

class IncomingMessageController extends Controller
{
    private $sentiment;

    private $mailgun;

    private $log;

    public function __construct(SentimentService $sentiment, Logger $log, MailgunService $mailgun)
    {
        $this->sentiment = $sentiment;
        $this->mailgun = $mailgun;
        $this->log = $log;
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
            'campaign_id'   => $campaign->id,
            'recipient_id'  => $recipient->id,
            'message'       => $request->get('stripped-text'),
            'message_id'    => $request->get('Message-Id'),
            'in_reply_to'   => $request->get('In-Reply-To'),
            'subject'       => $request->get('subject'),
            'type'          => 'email',
            'recording_sid' => 0,
            'incoming'      => 1,
        ]);

        $response->save();

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
			if (! $recipient) {
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
            list($phoneNumber, $campaign, $recipient) = $this->getRequestObjects($request);

            $invalidCharacters = '/[^\w\s]*/';
            $message = preg_replace($invalidCharacters, '', $request->get('Body'));

            $response = new Response([
                'message'       => $message,
                'incoming'      => 1,
                'type'          => 'text',
                'recording_sid' => 0,
                'campaign_id'   => $campaign->id,
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

            $recipient->last_responded_at = \Carbon\Carbon::now('UTC');
            $recipient->save();

            $response->recipient_id = $recipient->id;
            $response->save();

            $this->sentiment->forResponse($response);

            event(new RecipientTextResponseReceived($response));
            event(new CampaignCountsUpdated($campaign));

            // ubsubscribe happens at twilio level
            if ($this->isUnsubscribeMessage($message)) {
                Log::debug('unsubscribing recipient #' . $recipient->id);
                $suppress = new \App\Models\SmsSuppression([
                    'phone'         => substr($recipient->phone, -10, 10),
                    'suppressed_at' => \Carbon\Carbon::now('UTC'),
                ]);
                $suppress->save();
            }

            return response('<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
                '<Response><Dial record="record-from-answer">' . $phoneNumber->forward . '</Dial></Response>', 200)
                ->header('Content-Type', 'text/xml');
        } catch (ModelNotFoundException $e) {
            Log::error("Model not found: " . $e->getMessage());

            return response('<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
                '<Response><Reject /></Response>', 401)
                ->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            Log::error("Exception: " . $e->getMessage());

            return response('<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
                "<Response>{$e->getMessage()}</Response>", 401)
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
                    'call_sid'             => $request->get('CallSid'),
                    'call_phone_number_id' => $phone_number_id,
                    'incoming'             => 1,
                    'type'                 => 'phone',
                    'duration'             => $request->get('CallDuration')?: 0,
                    'campaign_id'          => $campaign->id,
                    'response_source'      => $request->get('From'),
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

            return response('<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
                '<Response>' . "\n" .
                '<Say voice="Polly.Joanna">This call may be recorded for quality assurance purposes</Say>' . "\n" .
                '<Dial record="record-from-answer">' . $phoneNumber->forward . '</Dial>' . "\n" .
                '</Response>', 200)
                ->header('Content-Type', 'text/xml');
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
    protected function getEmailMetadata($email) : array
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

        // Create a new Recipient and add it to the campaign for the person
        $recipient = new Recipient([
            'first_name'  => $sender->first_name,
            'last_name'   => $sender->last_name,
            'phone'       => $request->get('From'),
            'campaign_id' => $campaign->id,
        ]);

        $recipient->save();

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
        $data = new class()
        {
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

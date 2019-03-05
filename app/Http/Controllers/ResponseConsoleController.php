<?php

namespace App\Http\Controllers;

use App\Classes\MailgunService;
use App\Events\CampaignCountsUpdated;
use App\Events\RecipientTextResponseReceived;
use App\Events\RecipientEmailResponseReceived;
use App\Events\RecipientPhoneResponseReceived;
use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\PhoneNumber;
use App\Models\Recipient;
use App\Models\Response;
use App\Services\PusherBroadcastingService;
use App\Services\TwilioClient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResponseConsoleController extends Controller
{
    /**
     * @var \App\Classes\MailgunService
     */
    protected $mailgun;

    /**
     * ResponseConsoleController constructor.
     * @param MailgunService $mailgun
     */
    public function __construct(MailgunService $mailgun)
    {
        $this->mailgun = $mailgun;
        $this->pages = 15;
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     * @param string   $filter
     * @param null     $label
     * @return mixed
     */
    public function getRecipientData(Request $request, Campaign $campaign, $filter = 'all', $label = null)
    {
        if ($filter == 'all') {
            $recipients = Recipient::withResponses($campaign->id);
        }
        if ($filter == 'unread') {
            $recipients = Recipient::unread($campaign->id);
        }
        if ($filter == 'idle') {
            $recipients = Recipient::idle($campaign->id);
        }
        if ($filter == 'archived') {
            $recipients = Recipient::archived($campaign->id);
        }
        if ($filter == 'labelled') {
            $recipients = Recipient::withResponses($campaign->id)
                ->labelled($label, $campaign->id);
        }
        if ($filter == 'email') {
            $recipients = Recipient::withResponses($campaign->id)->whereIn(
                'recipients.id',
                result_array_values(
                    DB::select("select recipient_id from responses where campaign_id = {$campaign->id} and type='email'")
                )
            );
        }
        if ($filter == 'text') {
            $recipients = Recipient::withResponses($campaign->id)->whereIn(
                'recipients.id',
                result_array_values(
                    DB::select("select recipient_id from responses where campaign_id = {$campaign->id} and type='text'")
                )
            );
        }
        if ($filter == 'calls') {
            $recipients = Recipient::withResponses($campaign->id)->whereIn(
                'recipients.id',
                result_array_values(
                    DB::select("select recipient_id from responses where campaign_id = {$campaign->id} and type='phone'")
                )
            );
        }

        if (!isset($recipients)) {
            abort(401);
        }

        if ($request->has('search')) {
            $recipients->where(function ($query) use ($request) {
				$keywords = explode(' ', $request->search);
				foreach ($keywords as $keyword) {

					$query->orWhere('first_name', 'like', '%' . $keyword . '%')
						->orWhere('last_name', 'like', '%' . $keyword . '%')
						->orWhere('email', 'like', '%' . $keyword . '%')
						->orWhere('phone', 'like', '%' . $keyword . '%')
						->orWhere('make', 'like', '%' . $keyword . '%')
						->orWhere('model', 'like', '%' . $keyword . '%')
						->orWhere('year', 'like', '%' . $keyword . '%');
				}
            });
            // $recipients->searchByQuery($request->input('search'));
        }

        $recipients->join('responses as r1', function ($join) {
            $join->on('recipients.id', '=', 'r1.recipient_id');
        })
            ->leftJoin('responses as r2', function ($join) {
                $join->on('r1.recipient_id', '=', 'r2.recipient_id')
                    ->on('r1.created_at', '<', 'r2.created_at');
            })
            ->whereNull('r2.created_at')
            ->selectRaw('recipients.*, r1.created_at as last_seen')
            ->orderBy('last_seen', 'desc');

        $recipients = $recipients->paginate($this->pages);

        $recipients->totalCount = Recipient::withResponses($campaign->id)->count();
        $recipients->unread = Recipient::unread($campaign->id)->count();
        $recipients->idle = Recipient::idle($campaign->id)->count();
        $recipients->archived = Recipient::archived()->count();
        $recipients->email = Recipient::withResponses($campaign->id)->whereIn(
            'recipients.id',
            result_array_values(
                DB::select("select recipient_id from responses where campaign_id = {$campaign->id} and type='email'")
            )
        )->count();
        $recipients->calls = Recipient::withResponses($campaign->id)->whereIn(
            'recipients.id',
            result_array_values(
                DB::select("select recipient_id from responses where campaign_id = {$campaign->id} and type='phone'")
            )
        )->count();
        $recipients->sms = Recipient::withResponses($campaign->id)->whereIn(
            'recipients.id',
            result_array_values(
                DB::select("select recipient_id from responses where campaign_id = {$campaign->id} and type='text'")
            )
        )->count();

        $recipients->labelCounts = Recipient::withResponses($campaign->id)
            ->selectRaw("sum(interested) as interested, sum(not_interested) as not_interested,
                sum(appointment) as appointment, sum(service) as service, sum(wrong_number) as wrong_number,
                sum(car_sold) as car_sold, sum(heat) as heat, sum(callback) as callback,
                sum(case when (interested = 0 and not_interested = 0 and appointment = 0 and service = 0 and
                wrong_number = 0 and car_sold = 0 and heat = 0) then 1 else 0 end) as none")
            ->first();

        $viewData['campaign'] = $campaign;
        $viewData['recipients'] = $recipients;
        $viewData['filter'] = $filter;
        $viewData['label'] = $label;
        $viewData['counters'] = [
            'total'  => $recipients->totalCount,
            'unread'      => $recipients->unread,
            'idle'        => $recipients->idle,
            'archived'    => $recipients->archived,
            'email'       => $recipients->email,
            'calls'       => $recipients->calls,
            'sms'         => $recipients->sms,
        ];
        // Add the labelcounts
        foreach ($recipients->labelCounts->setAppends([])->toArray() as $key => $value) {
            $viewData['counters'][$key] = intval($value);
        }

        return $viewData;
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, Campaign $campaign, $filter = null)
    {
        $counters = [];
        $counters['total'] = Recipient::withResponses($campaign->id)->count();
        $counters['unread'] = Recipient::unread($campaign->id)->count();
        $counters['idle'] = Recipient::idle($campaign->id)->count();
        $counters['calls'] = Recipient::withResponses($campaign->id)->whereIn(
            'recipients.id',
            result_array_values(
                DB::select("select recipient_id from responses where campaign_id = {$campaign->id} and type='phone'")
            )
        )->count();
        $counters['email'] = Recipient::withResponses($campaign->id)->whereIn(
            'recipients.id',
            result_array_values(
                DB::select("select recipient_id from responses where campaign_id = {$campaign->id} and type='email'")
            )
        )->count();
        $counters['sms'] = Recipient::withResponses($campaign->id)->whereIn(
            'recipients.id',
            result_array_values(
                DB::select("select recipient_id from responses where campaign_id = {$campaign->id} and type='text'")
            )
        )->count();

        $labels = ['none', 'interested', 'appointment', 'callback', 'service', 'not_interested', 'wrong_number', 'car_sold', 'heat'];
        foreach ($labels as $label) {
            $counters[$label] = Recipient::withResponses($campaign->id)
                ->labelled($label, $campaign->id)
                ->count();
        }

        $data = [
            'counters' => $counters,
            'campaign' => $campaign
        ];

        if ($filter) {
            $data['filterApplied'] = $filter;
        }
        return view('campaigns.console', $data);
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     * @return mixed
     */
    public function getRecipientsForUserDisplay(Request $request, Campaign $campaign)
    {
        $filter = $request->get('filter') ?: 'all';
        $label = $request->get('label') ?: null;

        $viewData = $this->getRecipientData($request, $campaign, $filter, $label);

        $viewData['recipients']->withPath('/campaign/' . $campaign->id . '/response-console');

        return $viewData;
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showUnread(Request $request, Campaign $campaign)
    {
        $viewData = $this->getRecipientData($request, $campaign, 'unread');

        $viewData['recipients']->withPath('/campaign/' . $campaign->id . '/response-console/unread');
        $viewData['activeFilter'] = 'unread';

        return view('campaigns.console', $viewData);
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showIdle(Request $request, Campaign $campaign)
    {
        $viewData = $this->getRecipientData($request, $campaign, 'idle');

        $viewData['recipients']->withPath('/campaign/' . $campaign->id . '/response-console/idle');
        $viewData['activeFilter'] = 'idle';

        return view('campaigns.console', $viewData);
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showArchived(Request $request, Campaign $campaign)
    {
        $viewData = $this->getRecipientData($request, $campaign, 'archived');

        $viewData['recipients']->withPath('/campaign/' . $campaign->id . '/response-console/archived');
        $viewData['activeFilter'] = 'archived';

        return view('campaigns.console', $viewData);
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     * @param string   $label
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLabelled(Request $request, Campaign $campaign, $label = 'none')
    {
        $viewData = $this->getRecipientData($request, $campaign, 'labelled', $label);

        $viewData['recipients']->withPath('/campaign/' . $campaign->id . '/response-console/labelled/' . $label);
        $viewData['activeFilter'] = $label;

        return view('campaigns.console', $viewData);
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCalls(Request $request, Campaign $campaign)
    {
        $viewData = $this->getRecipientData($request, $campaign, 'calls');

        $viewData['recipients']->withPath('/campaign/' . $campaign->id . '/response-console/calls');
        $viewData['activeFilter'] = 'calls';

        return view('campaigns.console', $viewData);
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showEmails(Request $request, Campaign $campaign)
    {
        $viewData = $this->getRecipientData($request, $campaign, 'email');

        $viewData['recipients']->withPath('/campaign/' . $campaign->id . '/response-console/email');
        $viewData['activeFilter'] = 'email';

        return view('campaigns.console', $viewData);
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showTexts(Request $request, Campaign $campaign)
    {
        $viewData = $this->getRecipientData($request, $campaign, 'text');

        $viewData['recipients']->withPath('/campaign/' . $campaign->id . '/response-console/sms');
        $viewData['activeFilter'] = 'text';

        return view('campaigns.console', $viewData);
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
			Log::error('Received bad request from Mailgun: (cannot find message-id) ' . json_encode($request->all()), JSON_UNESCAPED_SLASHES);
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
     * Process inbound email
     *
     * @param \Illuminate\Http\Request $request
     */
    public function inboundEmail(Request $request)
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
     * Reply to a previous email
     *
     * @param \App\Models\Campaign     $campaign
     * @param \App\Models\Recipient    $recipient
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     * @throws \Pusher\PusherException
     */
    public function emailReply(Campaign $campaign, Recipient $recipient, Request $request)
    {
        if ($campaign->isExpired()) {
            abort(403, 'Illegal Request. This abuse of the system has been logged.');
        }

        $request->request->set('message', nl2br($request->get('message')));

        $lastMessage = Response::where('type', 'email')
            ->where('campaign_id', $campaign->id)
            ->where('incoming', 1)
            ->where('recipient_id', $recipient->id)
            ->orderBy('id', 'desc')
            ->first();

        $subject = 'Re: ' . $lastMessage->subject;

        # Send off the email
        $reply = $this->mailgun->sendClientEmail($campaign, $recipient, $subject, $request->get('message'),
            $request->get('message'));

        // Mark all previous messages as read
        Response::where('type', 'email')
            ->where('campaign_id', $campaign->id)
            ->where('recipient_id', $recipient->id)
            ->update(['read' => true]);

        # Save the response
        $response = new Response([
            'campaign_id'   => $campaign->id,
            'recipient_id'  => $recipient->id,
            'message'       => $request->get('message'),
            'message_id'    => $reply->getId(),
            'in_reply_to'   => $lastMessage->message_id,
            'subject'       => $subject,
            'incoming'      => 0,
            'type'          => 'email',
            'recording_sid' => 0,
            'duration'      => 0,
            'user_id'       => $request->user()->id,
        ]);
        $response->save();
        $response->load('impersonation.impersonator');

        # Log the transaction
        $log = new EmailLog([
            'message_id'   => str_replace(['<', '>'], '', $reply->getId()),
            'code'         => 0,
            'campaign_id'  => $campaign->id,
            'recipient_id' => $recipient->id,
            'event'        => 'reply',
            'recipient'    => $recipient->email,
        ]);
        $log->save();
        return response()->json(['response' => $response]);
    }

    /**
     * Send an SMS reply
     * @param \App\Models\Campaign     $campaign
     * @param \App\Models\Recipient    $recipient
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     * @throws \Pusher\PusherException
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    public function smsReply(Campaign $campaign, Recipient $recipient, Request $request)
    {
        if ($campaign->isExpired()) {
            abort(403, 'Illegal Request. This abuse of the system has been logged.');
        }

        $sms_phone_number = $campaign->phones()->whereCallSourceName('sms')->firstOrFail();
        $reply = \Twilio::sendSms($sms_phone_number->phone_number, $recipient->phone, $request->input('message'));

        // Mark all previous messages as read
        Response::where('type', 'text')
            ->where('campaign_id', $campaign->id)
            ->where('recipient_id', $recipient->id)
            ->update(['read' => true]);

        $response = Response::create([
            'campaign_id'   => $campaign->id,
            'recipient_id'  => $recipient->id,
            'message'       => $request->get('message'),
            'incoming'      => 0,
            'read'          => 1,
            'type'          => 'text',
            'user_id'       => $request->user()->id,
            'recording_sid' => 0,
        ]);
        $response->load('impersonation.impersonator');

        return response()->json(['response' => $response]);
    }

    /**
     * Process inbound phone stuff
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function inboundPhone(Request $request)
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
                '<Response><Dial record="record-from-answer">' . $phoneNumber->forward . '</Dial></Response>', 200)
                ->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            Log::error("inboundPhone(): {$e->getMessage()}");

            return response('<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
                '<Response><Reject>' . $e->getMessage() . '</Reject></Response>', 401)
                ->header('Content-Type', 'text/xml');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function inboundPhoneStatus(Request $request)
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
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function inboundText(Request $request)
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
     * Parse out the encoded email data
     *
     * @param $email
     *
     * @return array
     */
    protected function getEmailMetadata($email)
    {
        $metadata = preg_split('/(_|@)/', $email);

        $campaign_id = $metadata[1];
        $recipient_id = $metadata[2];

        return [$campaign_id, $recipient_id];
    }

    /**
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
     * @param \Illuminate\Http\Request $request
     * @param                          $campaign
     *
     * @return \App\Models\Recipient
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    protected function createRecipientFromSender(Request $request, $campaign)
    {
        # Lookup caller's "caller-name" from Twilio
        $sender = (object)(new TwilioClient)->getNameFromPhoneNumber($request->get('From'));

        # Create a new Recipient and add it to the campaign for the person
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
     * @param $from
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
     * @param $message
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
     * @param $message
     * @return bool
     */
    private function containsUnsubscribeVerbage($message)
    {
        return in_array($message, ['stop', 'unsubscribe', 'stopall', 'cancel', 'end', 'quit']);
    }

    /**
     * @param $message
     * @return string|string[]|null
     */
    private function simplifySmsMessage($message)
    {
        return preg_replace('/[^A-Za-z0-9]*/', '', strtolower(trim($message)));
    }
}

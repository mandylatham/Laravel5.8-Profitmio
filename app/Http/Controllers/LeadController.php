<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Builders\ResponseBuilder;
use ProfitMiner\Base\Services\Media\Transport\Messages\SmsMessage;
use ProfitMiner\Base\Services\Media\Transport\Messages\EmailMessage;
use ProfitMiner\Base\Services\Media\Transport\Contracts\SMSTransportContract;
use ProfitMiner\Base\Services\Media\Transport\Contracts\EmailTransportContract;

class LeadController extends Controller
{
    /**
     * @var SmsTransportContract Service
     */
    protected $sms;

    /**
     * @var EmailTransportContract $email
     */
    protected $email;

    /**
     * @var App\Services\SentimentService
     */
    protected $sentiment;

    /**
     * Constructor.
     * 
     * @param MailgunService   $mailgun   Dependency Injected Class
     * @param SentimentService $sentiment Dependency Injected Class
     */
    public function __construct(EmailTransportContract $email, SMSTransportContract $sms, SentimentService $sentiment)
    {
        $this->sms = $sms;
        $this->email = $email;
        $this->sentiment = $sentiment;
    }

    public function index(Request $request)
    {
        // Load view
    }

    public function search(Request $request)
    {
        // Apply search if needed
    }

    public function show(Lead $lead)
    {
        // Validate the request

        // Authorize the request

        // Gather object data from models

        // Convert raw data to custom object notation
    }

    public function open(Lead $lead)
    {
        // Sanity check: cuurent state is new

        // Open the Lead

        // Broadcast update to counts
    }

    public function close(Lead $lead)
    {
        // Sanity check: current state is open

        // Close the Lead

        // Broadcast update to counts
    }

    public function reopen(Lead $lead)
    {
        // Sanity check: current state is closed

        // ReOpen the Lead

        // Broadcast update to counts
    }
    /**
     * Send the lead an sms response
     * 
     * @param Campaign $campaign
     * @param Lead $lead
     * @param Request $request
     */
    public function sendSms(Campaign $campaign, Lead $lead, Request $request)
    {
        $this->authorize('create', [Response::class, $campaign]);

        $from = $campaign->phones()->whereCallSourceName('sms')->firstOrFail();

        $sms = new SmsMessage($from->phone_number, $lead->phone, $request->input('message'));

        $sid = $this->sms->send($sms);

        $response = ResponseBuilder::buildSmsReply($request->user(), $lead, $sms->getContent(), $sid);

        $this->sentiment->forResponse($response);

        return response()->json(['response' => $response]);
    }

    /**
     * Send the lead an email response
     * 
     * @param Campaign $campaign
     * @param Lead $lead
     * @param Request $request
     */
    public function sendEmail(Campaign $campaign, Lead $lead, Request $request)
    {
        $this->authorize('create', [Response::class, $campaign]);

        $lastMessage = Response::where('type', 'email')->where('campaign_id', $campaign->id)
                                                       ->where('incoming', 1)
                                                       ->where('recipient_id', $lead->id)
                                                       ->orderBy('id', 'desc')
                                                       ->firstOrFail();

        $subject = 'Re: ' . $lastMessage->subject;

        $message = $request->input('message');

        $email = new EmailMessage($this->getEmailFromLine($campaign, $lead), $lead->email, $subject, $message, nl2br($message));

        $sid = $this->email->send($email);

        $response = ResponseBuilder::builderEmailReply($request->user(), $lead, $subject, $lastMessage->message_id, $email->getContent(), $sid);

        $this->sentiment->forResponse($response);

        return response()->json(['response' => $response]);
    }

    public function updateNotes(Lead $lead, Request $request)
    {
        // 
    }

    public function markCalledBack(Lead $lead, Appointment $callback)
    {
        //
    }

    public function sendToServiceDepartment(Lead $lead)
    {
        //
    }

    public function sendToCrm(Lead $lead)
    {
        //
    }

    /**
     * The Email "from" address
     *
     *
     * @param Campaign  $campaign
     * @param Recipient $recipient
     * @param User      $client
     * @return string
     */
    private function getEmailFromLine(Campaign $campaign, Lead $lead)
    {
        $from = $campaign->dealership->name;
        $domain = env('MAILGUN_DOMAIN');

        return "{$from} <" . Str::slug("{$from}") . "_{$campaign->id}_{$lead->id}@{$domain}>";
    }
}

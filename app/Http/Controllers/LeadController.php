<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Campaign;
use App\Models\Response;
use Illuminate\Log\Logger;
use Illuminate\Support\Str;
use App\Services\CrmService;
use Illuminate\Http\Request;
use App\Repositories\LeadSearch;
use App\Builders\ResponseBuilder;
use App\Services\SentimentService;
use App\Events\CampaignCountsUpdated;
use App\Http\Resources\LeadCollection;
use App\Http\Resources\Lead as LeadResource;
use ProfitMiner\Base\Services\Media\Transport\Messages\SmsMessage;
use ProfitMiner\Base\Services\Media\Transport\Messages\EmailMessage;
use ProfitMiner\Base\Services\Media\Transport\Contracts\SMSTransportContract;
use ProfitMiner\Base\Services\Media\Transport\Contracts\EmailTransportContract;

class LeadController extends Controller
{
    /**
     * @var CrmService
     */
    protected $crm;

    /**
     * @var EmailTransportContract $email
     */
    protected $email;

    /**
     * @var LeadSearch
     */
    protected $leadSearch;

    /**
     * @var Logger
     */
    protected $log;

    /**
     * @var App\Services\SentimentService
     */
    protected $sentiment;

    /**
     * @var SmsTransportContract Service
     */
    protected $sms;

    /**
     * Constructor.
     *
     * @param MailgunService   $mailgun   Dependency Injected Class
     * @param SentimentService $sentiment Dependency Injected Class
     */
    public function __construct(
        Logger $log,
        CrmService $crm,
        LeadSearch $leadSearch,
        SMSTransportContract $sms,
        SentimentService $sentiment,
        EmailTransportContract $email
    ) {
        $this->crm = $crm;
        $this->log = $log;
        $this->sms = $sms;
        $this->email = $email;
        $this->sentiment = $sentiment;
        $this->leadSearch = $leadSearch;
    }

    /**
     * @param Campaign $campaign
     * @param Request  $request
     */
    public function index(Campaign $campaign, Request $request)
    {
        $status = $request->status;
        $search = $request->search;
        return new LeadCollection(
            $this->leadSearch
                 ->forCampaign($campaign)
                 ->byStatus($status)
                 ->byKeyword($search)
                 ->results()
        );
    }

    public function show(Campaign $campaign, Lead $lead)
    {
        // Validate the request

        // Authorize the request

        // Gather object data from models
        $threads = new ResponseThread($campaign, $lead);
        // nope, there is a lot more...

        // Convert raw data to custom object notation
    }

    public function open(Lead $lead)
    {
        // Sanity check: cuurent state is new
        if ($lead->status != 'New') {
            throw new \Exception("Invalid Operation");
        }

        // Log Lead Activity

        // Open the Lead
        $lead->open();

        // Broadcast update to counts
        event(new CampaignCountsUpdated($lead->campaign));

        return response()->json(['recipient' => $lead]);
    }

    public function close(Lead $lead)
    {
        // Sanity check: current state is open
        if ($lead->status != 'Open') {
            throw new \Exception("Invalid Operation");
        }

        // Log Lead Activity

        // Close the Lead
        $lead->close(auth()->user());

        // Broadcast update to counts
        event(new CampaignCountsUpdated($lead->campaign));

        return response()->json(['recipient' => $lead]);
    }

    public function reopen(Lead $lead)
    {
        // Sanity check: current state is closed
        if ($lead->status != 'Closed') {
            throw new \Exception("Invalid Operation");
        }

        // Log Lead Activity

        // ReOpen the Lead
        $lead->reopen();

        // Broadcast update to counts
        event(new CampaignCountsUpdated($lead->campaign));

        return response()->json(['recipient' => $lead]);
    }
    /**
     * Send the lead an sms response
     *
     * @param Campaign $campaign
     * @param Lead     $lead
     * @param Request  $request
     */
    public function sendSms(Campaign $campaign, Lead $lead, Request $request)
    {
        $this->authorize('create', [Response::class, $campaign]);

        $from = $campaign->phones()->whereCallSourceName('sms')->firstOrFail();

        $sms = new SmsMessage(
            $from->phone_number,
            $lead->phone,
            $request->input('message')
        );

        $sid = $this->sms->send($sms);

        $response = ResponseBuilder::buildSmsReply(
            $request->user(),
            $lead,
            $sms->getContent(),
            $sid
        );

        $this->sentiment->forResponse($response);

        return response()->json(['response' => $response]);
    }

    /**
     * Send the lead an email response
     *
     * @param Campaign $campaign
     * @param Lead     $lead
     * @param Request  $request
     */
    public function sendEmail(Campaign $campaign, Lead $lead, Request $request)
    {
        $this->authorize('create', [Response::class, $campaign]);

        $lastMessage = Response::where('type', 'email')
            ->where('campaign_id', $campaign->id)
            ->where('incoming', 1)
            ->where('recipient_id', $lead->id)
            ->orderBy('id', 'desc')
            ->firstOrFail();

        $subject = 'Re: ' . $lastMessage->subject;

        $message = $request->input('message');

        $email = new EmailMessage(
            $this->getEmailFromLine($campaign, $lead),
            $lead->email,
            $subject,
            $message,
            nl2br($message)
        );

        $sid = $this->email->send($email);

        $response = ResponseBuilder::buildEmailReply(
            $request->user(),
            $lead,
            $subject,
            $lastMessage->message_id,
            $email->getContent(),
            $sid
        );

        $this->sentiment->forResponse($response);

        return response()->json(['response' => $response]);
    }

    /**
     * @param Lead    $lead
     * @param Request $request
     */
    public function updateNotes(Lead $lead, Request $request)
    {
        $lead->fill(['notes' => $request->notes]);

        $lead->save();

        return $lead->toJson();
    }

    /**
     * @param Appointment $callback
     * @param Request     $request
     */
    public function markCalledBack(Appointment $callback, Request $request)
    {
        $callback->update(['called_back' => (int) $request->called_back]);

        event(new CampaignCountsUpdated($callback->campaign));

        return response()->json(
            [
            'called_back' => $callback->called_back,
            ]
        );
    }

    public function sendToServiceDepartment(Lead $lead)
    {
        //
    }

    /**
     * @param \App\Models\Lead         $lead
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    public function removeLabel(Lead $recipient, Request $request)
    {
        if ($request->label && in_array(
            $request->label, [
                'interested',
                'not_interested',
                'appointment',
                'service',
                'wrong_number',
                'car_sold',
                'heat',
                'callback',
            ]
        )
        ) {
            $lead->fill(
                [
                $request->label => 0,
                ]
            );
            $lead->save();

            event(new CampaignCountsUpdated($lead->campaign));

            $class = 'badge-danger';
            if (in_array($request->label, ['interested', 'appointment', 'service', 'callback'])) {
                $class = 'badge-success';
            }
        }
    }

    /**
     * @param \App\Models\Recipient    $recipient
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     * @throws \Exception
     */
    public function addLabel(Lead $lead, Request $request)
    {
        $sendNotifications = !! $lead->campaign->service_dept;

        if ($request->label && in_array(
            $request->label, [
                'interested',
                'not_interested',
                'appointment',
                'service',
                'wrong_number',
                'car_sold',
                'heat',
                'callback',
            ]
        )
        ) {
            $lead->fill(
                [
                $request->label => 1,
                ]
            );

            $lead->save();

            if ($request->input('label') == 'service' && !! $lead->campaign->service_dept) {
                event(new ServiceDeptLabelAdded($lead));
            }

            event(new CampaignCountsUpdated($lead->campaign));

            return response()->json(
                [
                "label" => $request->label,
                "labelText" => $this->getLabelText($request->label),
                ]
            );
        }

        return '';
    }

    /**
     * @param Recipient $recipient
     * @param array     $list
     * @return array
     */
    public function fetchResponsesByRecipient(Lead $lead, array $list = [])
    {
        $data = [];

        if (empty($list)) {
            $appointments = Appointment::where('recipient_id', $lead->id)->get()->toArray();
            $emailThreads = Response::where('campaign_id', $lead->campaign->id)
                ->where('recipient_id', $lead->id)
                ->where('type', 'email')
                ->get()
                ->toArray();
            $textThreads = Response::where('campaign_id', $lead->campaign->id)
                ->where('recipient_id', $lead->id)
                ->where('type', 'text')
                ->get()
                ->toArray();
            $phoneThreads = Response::where('campaign_id', $lead->campaign->id)
                ->where('recipient_id', $lead->id)
                ->where('type', 'phone')
                ->get()
                ->toArray();

            $data = [
                'appointments' => $appointments,
                'threads'      => [
                    'email' => $emailThreads,
                    'text'  => $textThreads,
                    'phone' => $phoneThreads,
                ],
                'recipient'    => $lead->toArray(),
            ];
        } else {
            foreach ($list as $item) {
                switch ($item) {
                case 'appointments':
                    $data['appointments'] = Appointment::where('lead_id', $lead->id)->get()->toArray();
                    break;
                case 'emails':
                    $data['threads']['email'] = Response::where('campaign_id', $lead->campaign->id)
                        ->where('recipient_id', $lead->id)
                        ->where('type', 'email')
                        ->get()
                        ->toArray();
                    break;
                case 'texts':
                    $data['threads']['text'] = Response::where('campaign_id', $lead->campaign->id)
                        ->where('recipient_id', $lead->id)
                        ->where('type', 'text')
                        ->get()
                        ->toArray();
                    break;
                case 'calls':
                    $data['threads']['phone'] = Response::where('campaign_id', $lead->campaign->id)
                        ->where('recpient_id', $lead->id)
                        ->where('type', 'phone')
                        ->get()
                        ->toArray();
                    break;
                case 'lead':
                    $data['recipient'] = $lead->toArray();
                    break;
                }
            }
        }

        return $data;
    }

    /**
     * The Email "from" address
     *
     * @param  Campaign  $campaign
     * @param  Recipient $recipient
     * @param  User      $client
     * @return string
     */
    private function getEmailFromLine(Campaign $campaign, Lead $lead)
    {
        $from = $campaign->dealership->name;
        $domain = env('MAILGUN_DOMAIN');

        return "{$from} <" . Str::slug("{$from}") . "_{$campaign->id}_{$lead->id}@{$domain}>";
    }

    /**
     * @param $label
     * @return mixed
     * @throws \Exception
     */
    private function getLabelText($label)
    {
        $labels = [
            'interested'     => 'Interested',
            'not_interested' => 'Not Interested',
            'appointment'    => 'Appointment',
            'service'        => 'Service Department',
            'wrong_number'   => 'Wrong Number',
            'car_sold'       => 'Car Sold',
            'heat'           => 'Heat Case',
        ];

        if (!in_array($label, array_keys($labels))) {
            throw new \Exception('Invalid Label Name provided by user form');
        }

        return $labels[$label];
    }
}

<?php

namespace App\Http\Controllers;

use App\Builders\ResponseBuilder;
use App\Http\Requests\SaveCheckinFormRequest;
use App\Events\{CampaignCountsUpdated, ServiceDeptLabelAdded};
use App\Factories\ActivityLogFactory;
use App\Http\Requests\CloseLeadRequest;
use App\Http\Resources\{LeadDetails, Lead as LeadResource, LeadCollection};
use App\Models\{Lead, User, Campaign, Response, Appointment};
use App\Repositories\LeadSearch;
use App\Services\{CrmService, CampaignUserScoreService, SentimentService};

use Illuminate\{
    Log\Logger,
    Support\Str,
    Support\Arr,
    Http\Request,
    Http\JsonResponse,
    Support\Facades\Auth,
    Http\Response as HttpResponse
};

use ProfitMiner\Base\Services\Media\Transport\{
    Messages\SmsMessage,
    Messages\EmailMessage,
    Contracts\SMSTransportContract,
    Contracts\EmailTransportContract
};

class LeadController extends Controller
{
    /**
     * @var ActivityLogFactory
     */
    protected $activityFactory;

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
     * @var CampaignUserScoreService
     */
    protected $scoring;

    /**
     * @var App\Services\SentimentService
     */
    protected $sentiment;

    /**
     * @var SmsTransportContract Service
     */
    protected $sms;

    /**
     * @var App\Models\User
     */
    protected $user;

    /**
     * Constructor.
     *
     * @param Logger                   $log        Log Service
     * @param CrmService               $crm        Crm Service
     * @param LeadSearch               $leadSearch Lead Search
     * @param SmsTransportContract     $sms        Sms Service
     * @param SentimentService         $sentiment  Sentiment Service
     * @param EmailTransportContract   $email      Email Service
     * @param CampaignUserScoreService $scoring  Campaign User Score Service
     */
    public function __construct(
        Logger $log,
        CrmService $crm,
        LeadSearch $leadSearch,
        SMSTransportContract $sms,
        SentimentService $sentiment,
        EmailTransportContract $email,
        CampaignUserScoreService $scoring,
        ActivityLogFactory $activityFactory
    ) {
        $this->crm = $crm;
        $this->log = $log;
        $this->sms = $sms;
        $this->email = $email;
        $this->sentiment = $sentiment;
        $this->leadSearch = $leadSearch;
        $this->scoring = $scoring;
        $this->activityFactory = $activityFactory;
    }

    /**
     * Index of Leads (searchable)
     *
     * @param Campaign $campaign
     * @param Request $request
     *
     * @return LeadCollection
     * @throws \Exception
     */
    public function index(Campaign $campaign, Request $request): LeadCollection
    {
        return new LeadCollection($this->leadSearch
            ->forCampaign($campaign)
            ->byRequest($request));
    }

    /**
     * JSON object for all lead details
     *
     * @param Campaign $campaign
     * @param Lead     $lead
     *
     * @return LeadDetails
     */
    public function show(Campaign $campaign, Lead $lead): LeadDetails
    {
        return new LeadDetails($lead);
    }

    /**
     * Open a lead
     *
     * @param Lead $lead
     *
     * @return LeadResource
     * @throws \Exception
     */
    public function open(Lead $lead): LeadResource
    {
        // Sanity check: current state is new
        if ($lead->status != Lead::NEW_STATUS) {
            throw new \Exception("Invalid Operation, status is {$lead->status}");
        }

        $lead->open();
        $activity = $this->activityFactory->forUserOpenedLead($lead);
        $this->scoring->forActivity($activity);

        event(new CampaignCountsUpdated($lead->campaign));

        return new LeadResource($lead);
    }

    public function saveCheckInForm(Lead $lead, SaveCheckinFormRequest $request)
    {
        $lead->update($request->only('first_name', 'last_name', 'email', 'phone', 'make', 'year', 'model'));

        if ($lead->campaign->adf_crm_export) {
            $this->sendToCrm($lead);
        }

        return $lead;
    }

    public function showCheckInForm(Lead $lead)
    {
        if ($lead->isClosed()) {
            $lead->open();
        }
        if (!$lead->checkedIn()) {
            $lead->setCheckedIn();
            $activity = $this->activityFactory->forUserCheckedLeadIn($lead);
            $this->scoring->forActivity($activity);
        }
        return view('lead.check-in-form')->with([
            'lead' => $lead,
        ]);
    }

    /**
     * Close a lead
     *
     * @param Lead $lead
     * @param CloseLeadRequest $request
     *
     * @return LeadResource
     * @throws \Exception
     */
    public function close(Lead $lead, CloseLeadRequest $request): LeadResource
    {
        // Sanity check: current state is open
        if ($lead->status != Lead::OPEN_STATUS) {
            throw new \Exception("Invalid Operation");
        }

        // Add to suppression if needed
        if (Arr::has($request->input('tags'), 'suppress')) {
            // @TODO Build suppression service
        }

        $lead->tags = $request->input('tags');
        $lead->outcome = $request->input('outcome');
        $lead->save();

        $lead->close();
        $activity = $this->activityFactory->forUserClosedLead($lead);
        $this->scoring->forActivity($activity);

        event(new CampaignCountsUpdated($lead->campaign));

        return new LeadResource($lead);
    }

    /**
     * Reopen a lead
     *
     * @param Lead $lead
     *
     * @return LeadResource
     * @throws \Exception
     */
    public function reopen(Lead $lead): LeadResource
    {
        // Sanity check: current state is closed
        if ($lead->status != Lead::CLOSED_STATUS) {
            throw new \Exception("Invalid Operation");
        }

        // Remove from Suppression if needed
        if (Arr::has($lead->tags, 'suppress')) {
            // @TODO Build suppression service
        }

        // Reset the lead tags
        $lead->tags = [];

        $lead->reopen();
        $activity = $this->activityFactory->forUserReopenedLead($lead);
        $this->scoring->forActivity($activity);

        event(new CampaignCountsUpdated($lead->campaign));

        return new LeadResource($lead);
    }

    /**
     * Send the lead an sms response
     *
     * @param Campaign $campaign
     * @param Lead $lead
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \ProfitMiner\Base\Services\Media\Exceptions\TransportException
     */
    public function sendSms(Campaign $campaign, Lead $lead, Request $request): JsonResponse
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
            auth()->user(),
            $lead,
            $sms->getContent(),
            $sid
        );

        $this->sentiment->forResponse($response);

        $activity = $this->activityFactory->forUserTextedLead($lead, $response);
        $this->scoring->forActivity($activity);

        return response()->json(['response' => $response]);
    }

    /**
     * Send the lead an email response
     *
     * @param Campaign $campaign
     * @param Lead $lead
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \ProfitMiner\Base\Services\Media\Exceptions\TransportException
     */
    public function sendEmail(Campaign $campaign, Lead $lead, Request $request): JsonResponse
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

        $domain = env('MAILGUN_DOMAIN');
        $from = $campaign->dealership->name;
        $email = new EmailMessage(
            $from,
            Str::slug("{$from}") . "_{$campaign->id}_{$lead->id}@{$domain}",
            $lead->email,
            $subject,
            $message,
            nl2br($message)
        );

        $sid = $this->email->send($email);

        $response = ResponseBuilder::buildEmailReply(
            auth()->user(),
            $lead,
            $subject,
            $lastMessage->message_id,
            $email->getContent(),
            $sid
        );

        $this->sentiment->forResponse($response);

        $activity = $this->activityFactory->forUserEmailedLead($lead, $response);
        $this->scoring->forActivity($activity);

        return response()->json(['response' => $response]);
    }

    /**
     * Update the lead notes
     *
     * @param Lead $lead
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateNotes(Lead $lead, Request $request): LeadResource
    {
        $lead->fill(['notes' => $request->notes]);
        $lead->save();

        return new LeadResource($lead);
    }

    /**
     * Mark a callback as called
     *
     * @param Appointment $callback
     * @param Request     $request
     *
     * @return JsonResponse
     */
    public function markCalledBack(Appointment $callback, Request $request): JsonResponse
    {
        $callback->update(['called_back' => (int) $request->called_back]);

        event(new CampaignCountsUpdated($callback->campaign));

        $lead = Lead::find($callback->recipient_id);
        $activity = $this->activityFactory->forUserCalledLeadBack($lead, $callback);
        $this->scoring->forActivity($activity);

        return response()->json(
            [
                'called_back' => $callback->called_back,
            ]
        );
    }

    /**
     * Send the lead to the campaign CRM
     *
     * @param Lead $lead
     *
     * @return HttpResponse
     */
    public function sendToCrm(Lead $lead): JsonResponse
    {
        try {
            $this->crm->sendRecipient($lead, Auth::user());

            $activity = $this->activityFactory->forUserSentLeadToCrm($lead);
            $this->scoring->forActivity($activity);

            return response()->json(['message' => 'Successfully sent lead to CRM']);
        } catch (\Exception $e) {
            $this->log->error("Unable to send lead to crm: " . $e->getMessage());
            abort(500, 'Unable to send to CRM');
        }
    }

    /**
     * Send the lead to the service department
     *
     * @param Lead $lead
     *
     * @return HttpResponse
     */
    public function sendToServiceDepartment(Lead $lead): JsonResponse
    {
        $lead->update(['service' => 1]);

        $activity = $this->activityFactory->forUserSentLeadToService($lead);
        $this->scoring->forActivity($activity);

        event(new ServiceDeptLabelAdded($lead));

        return response()->json(['message' => 'Successfully sent lead to Service Department']);
    }

    /**
     * The Email "from" address
     *
     * @param Campaign $campaign
     * @param Lead $lead
     * @return string
     */
    private function getEmailFromLine(Campaign $campaign, Lead $lead)
    {
        $from = $campaign->dealership->name;
        $domain = env('MAILGUN_DOMAIN');

        return "{$from} <" . Str::slug("{$from}") . "_{$campaign->id}_{$lead->id}@{$domain}>";
    }

    /**
     * @param string $label
     * @return string
     */
    private function getLabelText(string $label) : string
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

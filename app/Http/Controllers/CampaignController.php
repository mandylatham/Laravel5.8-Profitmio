<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Response;
use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Company;
use App\Models\LeadTag;
use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\Recipient;
use App\Models\PhoneNumber;
use Illuminate\Http\Request;
use App\Http\Requests\NewCampaignRequest;
use App\Http\Resources\Campaign as CampaignResource;
use Spatie\Activitylog\Models\Activity;

const SECONDS_PER_HOUR = 3600;
const SECONDS_PER_DAY = 86400;

class CampaignController extends Controller
{
    private $emailLog;

    private $campaign;

    private $company;

    private $recipient;

    public function __construct(Campaign $campaign, Company $company, EmailLog $emailLog, Recipient $recipient)
    {
        $this->campaign = $campaign;
        $this->company = $company;
        $this->emailLog = $emailLog;
        $this->recipient = $recipient;
    }

    public function index(Request $request)
    {
        return view('campaigns.index', []);
    }


    /**
     * Load Campaign Console Page
     *
     * @param Request  $request
     * @param Campaign $campaign
     *
     * @return \Illuminate\View\View
     */
    public function console(Request $request, Campaign $campaign, $filter = null)
    {
        $counters = [];
        $counters['total'] = $campaign->leads()->count();
        $counters['new'] = $campaign->leads()->new()->count();
        $counters['open'] = $campaign->leads()->open()->count();
        $counters['closed'] = $campaign->leads()->closed()->count();
        $counters['calls'] = $campaign->leads()->whereHas('responses', function ($q) { $q->whereType('phone'); })->count();
        $counters['email'] = $campaign->leads()->whereHas('responses', function ($q) { $q->whereType('email'); })->count();
        $counters['sms'] = $campaign->leads()->whereHas('responses', function ($q) { $q->whereType('text'); })->count();

        $positiveTags = LeadTag::whereIn('campaign_id', [0, $campaign->id])
            ->whereIn('indication', ['positive', 'neutral'])
            ->select(['name', 'text'])
            ->get();
        $textToValueRequestedTag = LeadTag::where('name', LeadTag::VEHICLE_VALUE_REQUESTED_TAG)
            ->select(['name', 'text'])
            ->first();
        $checkedInTextToValueTag = LeadTag::where('name', LeadTag::CHECKED_IN_FROM_TEXT_TO_VALUE_TAG)
            ->select(['name', 'text'])
            ->first();
        $negativeTags = LeadTag::whereIn('campaign_id', [0, $campaign->id])
            ->whereIn('indication', ['negative', 'neutral'])
            ->select(['name', 'text'])
            ->get();
        $leadTags = LeadTag::whereIn('campaign_id', [0, $campaign->id])
            ->orderBy('text', 'ASC')
            ->select(['name', 'text'])
            ->get();

        $data = [
            'counters' => $counters,
            'campaign' => $campaign,
            'leadTags' => $leadTags,
            'checkedInTextToValueTag' => $checkedInTextToValueTag,
            'textToValueRequestedTag' => $textToValueRequestedTag,
            'positiveTags' => $positiveTags,
            'negativeTags' => $negativeTags
        ];

        if ($filter) {
            $data['filterApplied'] = $filter;
        }
        return view('campaigns.console', $data);
    }

    /**
     * Return all campaigns for user display
     * @param Request $request
     * @return mixed
     */
    public function getForUserDisplay(Request $request)
    {
        $campaignQuery = Campaign::searchByRequest($request);
        $campaigns = $campaignQuery
            ->orderBy('status', 'asc')
            ->orderBy('campaigns.id', 'desc')
            ->paginate(15);

        foreach ($campaigns as $campaign) {
            $counters = [];
            $counters['total'] = $campaign->leads()->count();
            $counters['new'] = $campaign->leads()->new()->count();
            $counters['open'] = $campaign->leads()->open()->count();
            $counters['closed'] = $campaign->leads()->closed()->count();
            $campaign['counters'] = $counters;
        }

        return $campaigns;
    }

    public function getList(Request $request)
    {
        $campaigns = $this->campaign->with(['client', 'mailers'])
            ->selectRaw("
                (select count(distinct(recipient_id)) from recipients where campaign_id = campaigns.id) as recipientCount),
                (select count(distinct(recipient_id)) from responses where campaign_id = campaigns.id and type='phone' and recording_sid is not null) as phoneCount,
                (select count(distinct(recipient_id)) from responses where campaign_id = campaigns.id and type='email') as emailCount,
                (select count(distinct(recipient_id)) from responses where campaign_id = campaigns.id and type='text') as textCount,
                users.id as client_id
            ")
            ->get();

        return $campaigns->toJson();
    }

    /**
     * Show a specific campaign
     *
     * @param \App\Models\Campaign $campaign
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Campaign $campaign)
    {
        $viewData = [];

        $viewData['campaign'] = $campaign;

        $emailStats = $campaign->getEmailLogsStats();
        $responseStats = $campaign->getRecipientStats();

        $viewData['emailCount'] = $emailStats->count();
        $viewData['emailStats'] = $emailStats->first();
        $viewData['responseCount'] = $responseStats->count();
        $viewData['responseStats'] = $responseStats->first();

        return view('campaigns.dashboard', $viewData);
    }

    public function details(Campaign $campaign)
    {
        $campaign->with('phones');
        return view('campaigns.details', [
            'campaign' => $campaign
        ]);
    }

    public function create()
    {
        $dealerships = Company::getDealerships();
        $agencies = Company::getAgencies();
        $viewData = [
            'dealerships' => $dealerships,
            'agencies' => $agencies,
        ];

        return view('campaigns.create', $viewData);
    }

    public function store(NewCampaignRequest $request)
    {
        $expires_at = null;
        $starts_at = (new Carbon($request->start, \Auth::user()->timezone))->timezone('UTC')->toDateTimeString();
        $ends_at = (new Carbon($request->end, \Auth::user()->timezone))->timezone('UTC')->toDateTimeString();

        if (! empty($request->input('expires'))) {
            $expires_at = (new Carbon($request->expires, \Auth::user()->timezone))->timezone('UTC')->toDateTimeString();
        } else {
            $expires_at = (new Carbon($request->end, \Auth::user()->timezone))->timezone('UTC')->addWeeks(2);
        }

        $status = $request->status;
        if ($expires_at <= \Carbon\Carbon::now('UTC')) {
            $status = 'Expired';
        }
        $campaign = Campaign::create([
            'name' => $request->input('name'),
            'status' => $status,
            'order_id' => $request->input('order'),
            'starts_at' => $starts_at,
            'ends_at' => $ends_at,
            'agency_id' => $request->input('agency'),
            'dealership_id' => $request->input('dealership'),
            'enable_text_to_value' => (bool) $request->input('enable_text_to_value', false),
            'adf_crm_export' => (bool) $request->input('adf_crm_export'),
            'adf_crm_export_email' => $request->input('adf_crm_export_email', []),
            'client_passthrough' => (bool) $request->input('client_passthrough'),
            'client_passthrough_email' => $request->input('client_passthrough_email', []),
            'lead_alerts' => (bool) $request->input('lead_alerts'),
            'lead_alert_email' => $request->input('lead_alert_emails', []),
            'service_dept' => (bool) $request->input('service_dept'),
            'service_dept_email' => $request->input('service_dept_email', []),
            'sms_on_callback' => (bool) $request->input('service_dept'),
            'sms_on_callback_number' => $request->input('sms_on_callback_number', []),
            'text_to_value_message' => $request->input('text_to_value_message', '')
        ]);

        if (! $campaign->expires_at) {
            $campaign->update(['expires_at' => $campaign->ends_at->addMonth()]);
        }

        if ($request->has('phone_number_ids')) {
            foreach ((array)$request->input('phone_number_ids') as $phone_number_id) {
                PhoneNumber::find($phone_number_id)->update(['campaign_id' => $campaign->id]);
            }
        }

        return response()->json(['message' => 'Resource created']);
    }

    public function edit(Campaign $campaign)
    {
        $dealerships = Company::getDealerships();
        $agencies = Company::getAgencies();
        $viewData = [
            'campaign' => new CampaignResource($campaign),
            'dealerships' => $dealerships,
            'agencies' => $agencies,
        ];

        return view('campaigns.edit', $viewData);
    }

    public function stats(Campaign $campaign)
    {
        return view('campaigns.stats', [
            'campaign' => $campaign
        ]);
    }

    public function getStatsData(Campaign $campaign, Request $request)
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $request->input('start_date') ?? Carbon::now()->subMonths(1)->toDateString());
        $endDate = Carbon::createFromFormat('Y-m-d', $request->input('end_date') ?? Carbon::now()->toDateString());
        $newLeadsOverTime = $campaign->leads()
            ->new()
            ->selectRaw('DATE(last_status_changed_at) as date, COUNT(id) as total')
            ->groupBy(DB::raw('DATE(last_status_changed_at)'))
            ->whereDate('last_status_changed_at', '>=', $startDate)
            ->whereDate('last_status_changed_at', '<=', $endDate)
            ->orderBy('last_status_changed_at', 'ASC')
            ->get();
        $leadsOpenOverTime = $campaign->leads()
            ->open()
            ->selectRaw('DATE(last_status_changed_at) as date, COUNT(id) as total')
            ->groupBy(DB::raw('DATE(last_status_changed_at)'))
            ->whereDate('last_status_changed_at', '>=', $startDate)
            ->whereDate('last_status_changed_at', '<=', $endDate)
            ->orderBy('last_status_changed_at', 'ASC')
            ->get();
        $leadsClosedOverTime = $campaign->leads()
            ->closed()
            ->selectRaw('DATE(last_status_changed_at) as date, COUNT(id) as total')
            ->groupBy(DB::raw('DATE(last_status_changed_at)'))
            ->whereDate('last_status_changed_at', '>=', $startDate)
            ->whereDate('last_status_changed_at', '<=', $endDate)
            ->orderBy('last_status_changed_at', 'ASC')
            ->get();
        $appointmentsOverTime = $campaign->appointments()
            ->selectRaw('DATE(appointment_at) as date, COUNT(id) as total')
            ->groupBy(DB::raw('DATE(appointment_at)'))
            ->whereDate('appointment_at', '>=', $startDate)
            ->whereDate('appointment_at', '<=', $endDate)
            ->where('type', Appointment::TYPE_APPOINTMENT)
            ->orderBy('appointment_at', 'ASC')
            ->get();
        $callbacksOverTime = $campaign->appointments()
            ->selectRaw('DATE(appointment_at) as date, COUNT(id) as total')
            ->groupBy(DB::raw('DATE(appointment_at)'))
            ->whereDate('appointment_at', '>=', $startDate)
            ->whereDate('appointment_at', '<=', $endDate)
            ->where('type', Appointment::TYPE_CALLBACK)
            ->orderBy('appointment_at', 'ASC')
            ->get();
        // Average time to open
        $firstResponsePerRecipient = $campaign->responses()
            ->selectRaw('MIN(created_at) as created_at, recipient_id')
            ->groupBy('recipient_id');
        $lastOpenActivityPerRecipient = Activity::selectRaw('MAX(created_at) as created_at, subject_id as recipient_id')
            ->where('subject_type', Lead::class)
            ->where('description', LeadActivity::OPENED)
            ->whereIn('subject_id', function ($query) use ($campaign) {
                $query->select('id')
                    ->from('recipients')
                    ->where('recipients.campaign_id', $campaign->id);
            })
            ->groupBy('subject_id');
        $averageTimeToOpen = DB::query()
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, q1.created_at, q2.created_at)) AS total')
            ->from(DB::raw("({$firstResponsePerRecipient->toSql()}) as q1"))
            ->mergeBindings($firstResponsePerRecipient->getQuery()->getQuery())
            ->join(DB::raw("({$lastOpenActivityPerRecipient->toSql()}) as q2"), function ($join) {
                $join->on('q1.recipient_id', '=', 'q2.recipient_id');
            })
            ->mergeBindings($lastOpenActivityPerRecipient->getQuery())
            ->first();
        if ($averageTimeToOpen->total) {
            $averageTimeToOpen = $averageTimeToOpen->total;
        } else {
            $averageTimeToOpen = 0;
        }
        // Average time to close
        $firstResponsePerRecipient = $campaign->responses()
            ->selectRaw('MIN(created_at) as created_at, recipient_id')
            ->groupBy('recipient_id');
        $lastCloseActivityPerRecipient = Activity::selectRaw('MAX(created_at) as created_at, subject_id as recipient_id')
            ->where('subject_type', Lead::class)
            ->where('description', LeadActivity::CLOSED)
            ->whereIn('subject_id', function ($query) use ($campaign) {
                $query->select('id')
                    ->from('recipients')
                    ->where('recipients.campaign_id', $campaign->id);
            })
            ->groupBy('subject_id');
        $averageTimeToClose = DB::query()
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, q1.created_at, q2.created_at)) AS total')
            ->from(DB::raw("({$firstResponsePerRecipient->toSql()}) as q1"))
            ->mergeBindings($firstResponsePerRecipient->getQuery()->getQuery())
            ->join(DB::raw("({$lastCloseActivityPerRecipient->toSql()}) as q2"), function ($join) {
                $join->on('q1.recipient_id', '=', 'q2.recipient_id');
            })
            ->mergeBindings($lastCloseActivityPerRecipient->getQuery())
            ->first();
        if ($averageTimeToClose->total) {
            $averageTimeToClose = $averageTimeToClose->total;
        } else {
            $averageTimeToClose = 0;
        }
        // Outcomes
        $leadsWithOutcome = $campaign->leads()
            ->whereDate('last_status_changed_at', '>=', $startDate)
            ->whereDate('last_status_changed_at', '<=', $endDate)
            ->whereNotNull('recipients.outcome')
            ->get();
        $resumeOutcomes = [
            Lead::POSITIVE_OUTCOME => [
                'total' => 0,
                'tags' => (object) []
            ],
            Lead::NEGATIVE_OUTCOME => [
                'total' => 0,
                'tags' => (object) []
            ]
        ];
        foreach ($leadsWithOutcome as $ld) {
            $resumeOutcomes[$ld->outcome]['total']++;
            foreach ($ld->tags as $tag) {
                if (!property_exists($resumeOutcomes[$ld->outcome]['tags'], $tag)) {
                    $resumeOutcomes[$ld->outcome]['tags']->$tag = 0;
                }
                $resumeOutcomes[$ld->outcome]['tags']->$tag++;
            }
        }
        // Overall ranking for point-holders
        $ranking = $campaign->getOverallRanking();
        // Leads by email
        $leadsByEmail = $campaign->leads()
            ->join('responses', 'responses.recipient_id', '=', 'recipients.id')
            ->where('responses.type', 'email')
            ->whereDate('responses.created_at', '>=', $startDate)
            ->whereDate('responses.created_at', '<=', $endDate)
            ->groupBy('recipients.id')
            ->selectRaw('recipients.id')
            ->get()
            ->count();
        $leadsByPhone = $campaign->leads()
            ->join('responses', 'responses.recipient_id', '=', 'recipients.id')
            ->where('responses.type', 'phone')
            ->whereDate('responses.created_at', '>=', $startDate)
            ->whereDate('responses.created_at', '<=', $endDate)
            ->groupBy('recipients.id')
            ->selectRaw('recipients.id')
            ->get()
            ->count();
        $leadsBySms = $campaign->leads()
            ->join('responses', 'responses.recipient_id', '=', 'recipients.id')
            ->where('responses.type', 'text')
            ->whereDate('responses.created_at', '>=', $startDate)
            ->whereDate('responses.created_at', '<=', $endDate)
            ->groupBy('recipients.id')
            ->selectRaw('recipients.id')
            ->get()
            ->count();
        // List of open leads by time
        $leadsOpenedWithTime = DB::query()
            ->selectRaw('TIMESTAMPDIFF(SECOND, q1.created_at, q2.created_at) AS total')
            ->from(DB::raw("({$firstResponsePerRecipient->toSql()}) as q1"))
            ->mergeBindings($firstResponsePerRecipient->getQuery()->getQuery())
            ->join(DB::raw("({$lastOpenActivityPerRecipient->toSql()}) as q2"), function ($join) {
                $join->on('q1.recipient_id', '=', 'q2.recipient_id');
            })
            ->mergeBindings($lastOpenActivityPerRecipient->getQuery())
            ->get();
        // Count number of leads per range of hours
        // The range of hours follow a 1hour gap until 12hrs. i.e: 0-1hr, 1-2hr, 2-3hr, ... ,12+hrs
        $leadsOpenByTime = array_fill(0, 12, 0);
        foreach ($leadsOpenedWithTime as $lead) {
            $hours = floor($lead->total / SECONDS_PER_HOUR);
            if ($hours > 11) {
                $leadsOpenByTime[11]++;
            } else {
                $leadsOpenByTime[$hours]++;
            }
        }
        // List of closed leads by time
        $leadsClosedWithTime = DB::query()
            ->selectRaw('TIMESTAMPDIFF(SECOND, q1.created_at, q2.created_at) AS total')
            ->from(DB::raw("({$firstResponsePerRecipient->toSql()}) as q1"))
            ->mergeBindings($firstResponsePerRecipient->getQuery()->getQuery())
            ->join(DB::raw("({$lastCloseActivityPerRecipient->toSql()}) as q2"), function ($join) {
                $join->on('q1.recipient_id', '=', 'q2.recipient_id');
            })
            ->mergeBindings($lastCloseActivityPerRecipient->getQuery())
            ->get();
        $leadsClosedByTime = array_fill(0, 7, 0);
        foreach ($leadsClosedWithTime as $lead) {
            $days = floor($lead->total / SECONDS_PER_DAY);
            if ($days > 6) {
                $leadsClosedByTime[6]++;
            } else {
                $leadsClosedByTime[$days]++;
            }
        }
        $viewData = [
            'campaign' => $campaign,
            'newLeadsOverTime' => $newLeadsOverTime,
            'leadsOpenOverTime' => $leadsOpenOverTime,
            'leadsClosedOverTime' => $leadsClosedOverTime,
            'appointmentsOverTime' => $appointmentsOverTime,
            'callbacksOverTime' => $callbacksOverTime,
            'averageTimeToOpen' => $averageTimeToOpen,
            'averageTimeToClose' => $averageTimeToClose,
            'outcomes' => $resumeOutcomes,
            'leadsClosedByTime' => $leadsClosedByTime,
            'leadsOpenByTime' => $leadsOpenByTime,
            'leadsByEmail' => $leadsByEmail,
            'leadsByPhone' => $leadsByPhone,
            'leadsBySms' => $leadsBySms,
            'ranking' => $ranking
        ];

        return $viewData;
    }

    public function update(Campaign $campaign, NewCampaignRequest $request)
    {
        if ($request->filled('phone_number_id') || $request->filled('forward')) {
            $phone = \App\Models\PhoneNumber::findOrFail($request->phone_number_id);
            $phone->fill(['forward' => $request->forward]);
            $phone->save();
        }

        $expires_at = null;
        $starts_at = (new Carbon($request->start, \Auth::user()->timezone))->timezone('UTC')->toDateTimeString();
        $ends_at = (new Carbon($request->end, \Auth::user()->timezone))->timezone('UTC')->toDateTimeString();

        if (! empty($request->input('expires'))) {
            $expires_at = (new Carbon($request->expires, \Auth::user()->timezone))->timezone('UTC')->toDateTimeString();
        } else {
            $expires_at = (new Carbon($request->end, \Auth::user()->timezone))->timezone('UTC')->addWeeks(2);
        }

        $status = $request->status;
        if (! $expires_at || ($expires_at && $expires_at <= \Carbon\Carbon::now('UTC'))) {
            $status = 'Expired';
        }

        $campaign->fill([
            'adf_crm_export' => (bool) $request->input('adf_crm_export'),
            'adf_crm_export_email' => $request->input('adf_crm_export_email', []),
            'agency_id' => $request->input('agency'),
            'client_passthrough' => (bool) $request->input('client_passthrough'),
            'client_passthrough_email' => $request->input('client_passthrough_email', []),
            'dealership_id' => $request->input('dealership'),
            'ends_at' => $ends_at,
            'expires_at' => $expires_at,
            'lead_alerts' => (bool) $request->input('lead_alerts'),
            'lead_alert_email' => $request->input('lead_alert_email', []),
            'name' => $request->input('name'),
            'order_id' => $request->input('order'),
            'service_dept' => (bool) $request->input('service_dept'),
            'service_dept_email' => $request->input('service_dept_email', []),
            'sms_on_callback' => (bool) $request->input('sms_on_callback'),
            'sms_on_callback_number' => $request->input('sms_on_callback_number', []),
            'text_to_value_message' => $request->input('text_to_value_message', ''),
            'starts_at' => $starts_at,
            'status' => $status
        ]);

        if (!$campaign->hasTextToValueEnabled() && $request->input('enable_text_to_value')) {
            $campaign->enable_text_to_value = $request->input('enable_text_to_value');
        }

        $campaign->save();

        return response()->json(['message' => 'Resource updated.']);
    }

    public function delete(Campaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('campaigns.index');
    }

    public function toggleCampaignUserAccess(Campaign $campaign, User $user)
    {
        $campaign->users()->toggle($user->id);

        return response()->json(['message' => 'Resource updated.']);
    }
}

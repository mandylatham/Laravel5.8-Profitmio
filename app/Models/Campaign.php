<?php

namespace App\Models;

use App\Factories\ActivityLogFactory;
use Illuminate\Http\Request;
use Sofa\Eloquence\Eloquence;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use DB;

class Campaign extends \ProfitMiner\Base\Models\Campaign
{
    use LogsActivity, Eloquence;

    protected $searchableColumns = ['id', 'name', 'order_id'];

    protected static $logAttributes = ['id', 'agency_id', 'dealership_id', 'name'];

    protected $fillable = [];
    protected $guarded = [];

    protected $appends = [
        'is_expired',
        'text_responses_count',
        'phone_responses_count',
        'email_responses_count',
        'call_sources_in_use',
        'appointment_counts',
        'interested_counts',
    ];

    protected $casts = [
        'sms_on_callback_number' => 'array',
        'service_dept_email' => 'array',
        'adf_crm_export_email' => 'array',
        'lead_alert_email' => 'array',
        'client_passthrough_email' => 'array',
    ];

    /*
    public static $legacy_tags = [
        'appointment' => 'Scheduled an appointment',
        'callback' => 'Requested a callback',
        'car_sold' => 'We had incorrect vehicle information',
        'heat' => 'Is upset',
        'interested' => 'Is interested',
        'not_interested' => 'Is not interested',
        'service' => 'Interested in service',
        'wrong_number' => 'We had the wrong phone number',
    ];

    public static $default_tags = [
        "positive" => [
            "future-lead" => "Interested but not at this time",
            "purchased" => "Purchased a vehicle",
            "return-client" => "Return client",
            "serviced" => "Serviced their vehicle",
            "walk-in" => "Lead came in",
            "will-come-in" => "Lead will come in",
        ],
        "negative" => [
            "heat-current" => "Lead upset over current experience",
            "heat-prior" => "Lead upset over prior experience",
            "old-data-address" => "Lead moved out of the area",
            "old-data-vehicle" => "Lead no longer owns vehicle",
            "suppress" => "Never wants to be contacted",
            "wrong-data-vehicle" => "Lead never owned vehicle",
            "wrong-lead-identity-phone" => "Wrong Number",
            "wrong-lead-identity-email" => "Wrong Email Address",
        ],
    ];
     */

    /**
     * Related Company: agency
     */
    public function agency()
    {
        return $this->hasOne(Company::class, 'id', 'agency_id');
    }

    /**
     * Related appointments
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Related Company: dealership
     */
    public function dealership()
    {
        return $this->hasOne(Company::class, 'id', 'dealership_id');
    }

    /**
     * Related drops
     */
    public function drops()
    {
        return $this->hasMany(Drop::class, 'campaign_id', 'id');
    }

    /**
     * Related logs
     */
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'campaign_id', 'id');
    }

    /**
     * Related leads
     */
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Related Lead Activity
     */
    public function leadActivity()
    {
        return $this->hasManyThrough(RecipientActivity::class, Recipient::class);
    }

    /**
     * Related mail drops
     */
    public function mailers()
    {
        return $this->hasMany(Mailer::class, 'campaign_id', 'id');
    }

    /**
     * Related phones
     */
    public function phones()
    {
        return $this->hasMany(PhoneNumber::class);
    }

    /**
     * Related recipients
     */
    public function recipients()
    {
        return $this->hasMany(Recipient::class, 'campaign_id', 'id');
    }

    /**
     * Related RecipientLists
     */
    public function recipientLists()
    {
        return $this->hasMany(RecipientList::class, 'campaign_id', 'id');
    }

    /**
     * Related responses
     */
    public function responses()
    {
        return $this->hasMany(Response::class, 'campaign_id', 'id');
    }

    /**
     * Related drops (deprecated)
     */
    public function schedules()
    {
        return $this->hasMany(CampaignSchedule::class, 'campaign_id', 'id');
    }

    /**
     * Related lead tags
     */
    public function tags()
    {
        return $this->hasMany(LeadTag::class);
    }

    /**
     * Related users
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->using(CampaignUser::class)
                    ->withPivot(['points']);
    }

    /**
     * Related email responses
     */
    public function emailResponses()
    {
        return $this->hasMany(Response::class, 'campaign_id', 'id')->where('responses.type', 'email');
    }

    /**
     * Related phone responses
     */
    public function phoneResponses()
    {
        return $this->hasMany(Response::class, 'campaign_id', 'id')->where('responses.type', 'phone');
    }

    /**
     * Related sms responses
     */
    public function textResponses()
    {
        return $this->hasMany(Response::class, 'campaign_id', 'id')->where('responses.type', 'text');
    }

    //==============================================================================
    // Begin utility methods
    // -----------------------------------------------------------------------------

    /**
     * Get stats for recipients
     *
     * @TODO refactor into repository
     */
    public function getRecipientStats()
    {
        $stats = $this->recipients()
            ->selectRaw("sum(service) as service,
                sum(appointment) as appointment,
                sum(heat) as heat,
                sum(interested) as interested,
                sum(not_interested) as not_interested,
                sum(wrong_number) as wrong_number,
                sum(car_sold) as car_sold,
                count(*) as total");

        return $stats->get();
    }

    public function getOverallRanking()
    {
        $scores = CampaignUserScore::select('id', 'score', 'user_id')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id) as id')
                    ->from('campaign_user_scores')
                    ->where('campaign_id', $this->id)
                    ->groupBy('user_id');
            })
            ->where('campaign_id', $this->id)
            ->with('user')
            ->get();

        $total = $scores->sum('score');

        foreach ($scores as &$score) {
            if ($total === 0) {
                $score->percentage = 0;
            } else {
                $score->percentage = round($score->score * 100 / $total, 2);
            }
            $score->openLeads = Activity::where('causer_id', $score->user_id)
                ->where('activity_log.causer_type', User::class)
                ->where('activity_log.subject_type', Lead::class)
                ->where('description', LeadActivity::OPENED)
                ->where('log_name', ActivityLogFactory::LEAD_ACTIVITY_LOG)
                ->join('recipients', 'recipients.id', '=', 'activity_log.subject_id')
                ->where('recipients.campaign_id', $this->id)
                ->count();
            $closedLeads = Lead::closed()
                ->join('activity_log', 'activity_log.subject_id', '=', 'recipients.id')
                ->where('activity_log.causer_type', User::class)
                ->where('activity_log.causer_id', $score->user_id)
                ->where('activity_log.subject_type', Lead::class)
                ->where('recipients.campaign_id', $this->id)
                ->where('activity_log.description', LeadActivity::CLOSED)
                ->select('recipients.id', 'outcome', 'tags')
                ->get();
            $resumeClosedLeads = [
                Lead::POSITIVE_OUTCOME => [
                    'total' => 0
                ],
                Lead::NEGATIVE_OUTCOME => [
                    'total' => 0
                ]
            ];
            foreach ($closedLeads as $ld) {
                $resumeClosedLeads[$ld->outcome]['total']++;
            }
            $score->closedLeads = $resumeClosedLeads;
        }
        return $scores;
    }

    /**
     * Scope by company
     */
    public function scopeFilterByCompany($query, Company $company)
    {
        return $query->where(function ($query) use ($company) {
            $query->orWhere('agency_id', $company->id);
            $query->orWhere('dealership_id', $company->id);
        });
    }

    /**
     * Scope by Eloquence search
     */
    public function scopeFilterByQuery($query, $q)
    {
        return $query->search($q);
    }

    /**
     * Get campaigns for company
     *
     * @TODO refactor into repository
     */
    public static function getCompanyCampaigns(int $companyId)
    {
        return
            self::whereNull('deleted_at')
                ->where(function ($query) use ($companyId) {
                    $query->where('agency_id', $companyId)
                        ->orWhere('dealership_id', $companyId);
                })->get();
    }

    /**
     * Get phone for sms
     *
     * @TODO refactor into repository
     */
    public function getSmsPhoneAttribute()
    {
        return $this->hasMany(PhoneNumber::class)->whereCallSourceName('sms')->first();
    }

    /**
     * Get counts (deprecated?)
     */
    public function getTextResponsesCountAttribute()
    {
        return $this->textResponses()->count();
    }

    /**
     * Get counts (deprecated?)
     */
    public function getEmailResponsesCountAttribute()
    {
        return $this->emailResponses()->count();
    }

    /**
     * Get counts (deprecated?)
     */
    public function getPhoneResponsesCountAttribute()
    {
        return $this->phoneResponses()->count() ;
    }

    /**
     * Get the active call sources
     */
    public function getCallSourcesInUseAttribute()
    {
        return $this->phones()->select('call_source_name')->get()->pluck('call_source_name')->toArray();
    }

    /**
     * Get counts (deprecated?)
     */
    public function getAppointmentCountsAttribute()
    {
        return $this->appointments()
            ->whereNotNull('appointments.appointment_at')
            ->count();
    }

    /**
     * Get counts (deprecated?)
     */
    public function getInterestedCountsAttribute()
    {
        return $this->recipients()
                    ->whereInterested(true)
                    ->count();
    }

    /**
     * Search Functionality
     */
    public static function searchByRequest(Request $request)
    {
        $loggedUser = auth()->user();
        $query = self::query()
            ->with(['dealership', 'agency'])
            ->whereNull('deleted_at');

        if (!$loggedUser->isAdmin()) {
            $company = Company::findOrFail(get_active_company());
            if ($loggedUser->isCompanyUser($company->id)) {
                $campaignsId = \DB::table('campaign_user')
                    ->whereUserId($loggedUser->id)
                    ->select('campaign_id')
                    ->get()
                    ->pluck('campaign_id')
                    ->toArray();
                $query->whereIn('id', $campaignsId);
            }

            if ($company->isDealership()) {
                $query->where('dealership_id', $company->id);
            } else if ($company->isAgency()) {
                $query->where('agency_id', $company->id);
            }
        } else if ($loggedUser->isAdmin() && $request->has('user')) {
            $campaignsId = \DB::table('campaign_user')
                ->whereUserId($request->input('user'))
                ->select('campaign_id')
                ->get()
                ->pluck('campaign_id')
                ->toArray();
            $query->whereIn('id', $campaignsId);
        }

        if ($request->has('q')) {
            $query->filterByQuery($request->input('q'));
        }

        if ($request->has('company')) {
            $query->filterByCompany(Company::findOrFail($request->input('company')));
        }
        return $query;
    }

    /**
     * Return the HTML template to show the name of the company in datatables
     *
     * @TODO fix this grossness
     *
     * @return string
     */
    public function getNameForTemplate()
    {
        $template = "<h5 style='max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space:nowrap;'>";
        if (isset($this->order_id)) {
            $template .= "<small>Order $this->order_id </small><br>";
        }
        $template .= ucwords($this->name);
        if (!empty($this->starts_at) && !empty($this->ends_at)) {
            $template .= "<br><small>From <code> " . show_date($this->starts_at,
                    'm/d/Y') . "</code> to <code>" . show_date($this->ends_at, 'm/d/Y') . "</code></small><br>";
        } else {
            $template = "<br><small>No Dates</small><br>";
        }
        $template .= '<span class="badge badge-outline';
        if ($this->status === 'Upcoming') {
            $template .= ' badge-primary';
        } else {
            if ($this->status == 'Completed' || $this->status == 'Expired') {
                $template .= ' badge-default';
            } else {
                $template .= ' badge-success';
            }
        }
        $template .= '">' . $this->status . '</span>';
        $template .= '</h5>';
        $template .= '<div class="campaign-links">';
        $template .= '<a class="btn btn-pure btn-primary btn-round campaign-view" href="' . route('campaign.view',
                ['campaign' => $this->id]) . '"><i class="fa fa-search"></i></a>';
        $template .= '<a class="btn btn-pure btn-primary btn-round campaign-drops" href="' . route('campaign.drop.index',
                ['campaign' => $this->id]) . '"><i class="icon icon-lg wi-raindrops" style="font-size: 28px; margin: -5px"></i></a>';
        $template .= '<a class="btn btn-pure btn-primary btn-round campaign-recipients" href="' . route('campaigns.recipients.index',
                ['campaign' => $this->id]) . '"><i class="fa fa-users"></i></a>';
        $template .= '<a class="btn btn-pure btn-primary btn-round campaign-console" href="' . route('campaign.response-console.index',
                ['campaign' => $this->id]) . '"><i class="fa fa-terminal"></i></a>';
        $template .= '<a class="btn btn-pure btn-warning btn-round campaign-edit" href="' . route('campaign.edit',
                ['campaign' => $this->id]) . '"><i class="fa fa-pencil"></i></a>';
        $template .= '<button class="btn btn-pure btn-danger btn-round delete-button" data-deleteUrl="' . route('campaign.delete',
                ['campaign' => $this->id]) . '"><i class="fa fa-trash"></i></button>';
        $template .= '</div>';

        return $template;
    }

    /**
     * Get stats
     *
     * @TODO refactor out into repository
     */
    public function getEmailLogsStats()
    {
        $stats = $this->emailLogs()
            ->selectRaw("sum(if(event = 'sent', 1, 0)) as sent,
                sum(if(event = 'delivered', 1, 0)) as delivered,
                sum(if(event = 'opened', 1, 0)) as opened,
                sum(if(event = 'clicked', 1, 0)) as clicked,
                sum(if(event = 'bounced', 1, 0)) as bounced,
                sum(if(event = 'dropped', 1, 0)) as dropped,
                sum(if(event = 'unsubscribed', 1, 0)) as unsubscribed,
                count(*) as total");
        if ($stats->count() > 0 && $stats->first()->sent > 0) {
            $emailObject = $stats->first();
            $emailObject->droppedPercent = round(abs((($emailObject->sent -
                        ($emailObject->dropped)) / $emailObject->sent * 100) - 100), 2);

            $emailObject->bouncedPercent = round(abs((($emailObject->sent -
                        $emailObject->bounced) / $emailObject->sent * 100) - 100), 2);
            $stats = collect([$emailObject]);
        }

        return $stats;
    }
}

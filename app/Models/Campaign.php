<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Sofa\Eloquence\Eloquence;
use Spatie\Activitylog\Traits\LogsActivity;

class Campaign extends Model
{
    use LogsActivity, Eloquence;

    protected $searchableColumns = ['id', 'name', 'order_id'];

    protected $fillable = [
        'agency_id',
        'dealership_id',
        'name',
        'status',
        'order_id',
        'starts_at',
        'ends_at',
        'adf_crm_export',
        'adf_crm_export_email',
        'lead_alerts',
        'lead_alert_email',
        'client_passthrough',
        'client_passthrough_email',
        'service_dept',
        'service_dept_email',
        'phone_number_id',
        'expires_at',
        'sms_on_callback',
        'sms_on_callback_number',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'starts_at',
        'ends_at',
        'expires_at',
    ];

    protected static $logAttributes = ['id', 'agency_id', 'dealership_id', 'name'];

    protected $appends = ['is_expired'];

    protected $casts = [
        'sms_on_callback_number' => 'json',
        'service_dept_email' => 'json',
        'adf_crm_export_email' => 'json',
        'lead_alert_email' => 'json',
        'client_passthrough_email' => 'json'
    ];

    public function agency()
    {
        return $this->hasOne(Company::class, 'id', 'agency_id');
    }

    public function dealership()
    {
        return $this->hasOne(Company::class, 'id', 'dealership_id');
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'campaign_id', 'id');
    }

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

    public function users()
    {
        return $this->belongsToMany(User::class)->using(CampaignUser::class);
    }

    public function scopeFilterByCompany($query, Company $company)
    {
        return $query->where(function ($query) use ($company) {
            $query->orWhere('agency_id', $company->id);
            $query->orWhere('dealership_id', $company->id);
        });
    }

    public function scopeFilterByQuery($query, $q)
    {
        return $query->search($q);
    }

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
     * Return the HTML template to show the name of the company in datatables
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

    public function phone()
    {
        return $this->hasOne(PhoneNumber::class, 'id', 'phone_number_id');
    }

    public function phones()
    {
        return $this->hasMany(PhoneNumber::class, 'campaign_id', 'campaign_id');
    }

    public function responses()
    {
        return $this->hasMany(Response::class, 'campaign_id', 'id');
    }

    public function recipients()
    {
        return $this->hasMany(Recipient::class, 'campaign_id', 'id');
    }

    public function recipientLists()
    {
        return $this->hasMany(RecipientList::class, 'campaign_id', 'id');
    }

    public static function searchByRequest(Request $request)
    {
        $loggedUser = auth()->user();
        $query = self::query()
            ->with(['dealership', 'agency'])
            ->whereNull('deleted_at');

        if (!$loggedUser->isAdmin()) {
            $campaignsId = \DB::table('campaign_user')
                ->whereUserId($loggedUser->id)
                ->select('campaign_id')
                ->get()
                ->pluck('campaign_id')
                ->toArray();
            $query->whereIn('id', $campaignsId);
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

    public function email_responses()
    {
        return $this->hasMany(Response::class, 'campaign_id', 'id')->where('responses.type', 'email');
    }

    public function phone_responses()
    {
        return $this->hasMany(Response::class, 'campaign_id', 'id')->where('responses.type', 'phone');
    }

    public function text_responses()
    {
        return $this->hasMany(Response::class, 'campaign_id', 'id')->where('responses.type', 'text');
    }

    public function drops()
    {
        return $this->hasMany(Drop::class, 'campaign_id', 'id');
    }

    public function mailers()
    {
        return $this->hasMany(Mailer::class, 'campaign_id', 'id');
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at <= Carbon::now('UTC');
    }

    public function schedules()
    {
        return $this->hasMany(CampaignSchedule::class, 'campaign_id', 'id');
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at <= Carbon::now('UTC');
    }

    public function getIsNotExpiredAttribute()
    {
        return $this->expires_at && !$this->isExpired;
    }
}

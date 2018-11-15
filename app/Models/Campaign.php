<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Campaign extends Model
{
    use LogsActivity;

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
        'phone_number_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'starts_at',
        'ends_at'
    ];

    protected static $logAttributes = ['id', 'agency_id', 'dealership_id', 'name'];

    public function agency()
    {
        return $this->hasOne(Company::class, 'id', 'agency_id');
    }

    public function dealership()
    {
        return $this->hasOne(Company::class, 'id', 'dealership_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function getCompanyCampaigns(int $companyId)
    {
        return
            self::whereNull('deleted_at')
                ->where(function($query) use ($companyId) {
                    $query->where('agency_id', $companyId)
                        ->orWhere('dealership_id', $companyId);
                })->get();
    }

    public function phone()
    {
        return $this->hasOne(PhoneNumber::class, 'id', 'phone_number_id');
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

    public function schedules()
    {
        return $this->hasMany(CampaignSchedule::class, 'campaign_id', 'id');
    }
}

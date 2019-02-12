<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Drop extends Model
{
    use SoftDeletes;

    protected $table = 'campaign_schedules';

    public $dates = [
        'send_at', 'created_at', 'updated_at', 'deleted_at', 'completed_at',
    ];

    public $fillable = [
        'type', 'send_at', 'email_subject', 'email_text', 'email_html', 'recipient_group',
        'text_message', 'text_message_image', 'send_vehicle_image', 'campaign_id', 'status',
        'percentage_complete', 'completed_at', 'system_id'
    ];

    protected $primaryKey = 'id';

    protected $appends = [
        'sms_phones'
    ];

    public function getSmsPhonesAttribute()
    {
        return $this->campaign->phones()->whereCallSourceName('sms')->count();
    }


    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

//    public function recipients()
//    {
//        return $this->hasMany(Recipient::class, 'campaign_id', 'campaign_id');
//    }

    public function recipients()
    {
        return $this->belongsToMany(Recipient::class, 'deployment_recipients', 'deployment_id', 'recipient_id');
    }

    public function getSendAtAttribute($value)
    {
        if (\Auth::user() instanceof \App\Models\User) {
            return \Carbon\Carbon::parse($value)->timezone(\Auth::user()->timezone);
        }

        return \Carbon\Carbon::parse($value);
    }

    public function scopeInGroup($query, $group_id)
    {
        return $query->where('subgroup', $group_id);
    }

    public function scopeEmailDue($query)
    {
        return $query->where('send_at', '<=', Carbon::now())
            ->with(['campaign' => function ($q) {
                $q->whereRaw("expires_at >= current_timestamp");
            }])
            ->has('campaign')
            ->where('status', 'Pending')
            ->whereNotNull('system_id')
            ->whereIn('type', ['email', 'legacy']);
    }

    public function scopeEmailDueInMinutes($query, $minutes)
    {
        return $query->where('send_at', '<=', Carbon::now()->subMinutes($minutes))
            ->where('status', 'Pending')
            ->whereNotNull('system_id')
            ->whereIn('type', ['email', 'legacy']);
    }
}

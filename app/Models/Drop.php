<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Drop extends Model
{
    use SoftDeletes;

    protected $table = 'campaign_schedules';

    protected $searchablecolumns = ['send_at', 'started_at', 'status'];

    public $dates = [
        'send_at', 'created_at', 'started_at', 'updated_at', 'deleted_at', 'completed_at',
    ];

    public $fillable = [
        'type', 'send_at', 'email_subject', 'email_text', 'email_html', 'recipient_group',
        'text_message', 'text_message_image', 'send_vehicle_image', 'campaign_id', 'status',
        'percentage_complete', 'completed_at', 'system_id', 'started_at', 'completed_at',
    ];

    protected $primaryKey = 'id';

    protected $appends = [
        'sms_phones', 'send_at_formatted', 'completed_at_formatted',
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

    public static function searchByRequest(Request $request, Campaign $campaign)
    {
        $query = self::select([
                'send_at', 'type', 'campaign_id', 'started_at', 'recipient_group', 'status', 'text_message', 'percentage_complete', 'completed_at', 'campaign_schedules.id',
                \DB::raw("case when type in ('email', 'sms') then
                (select count(*) from deployment_recipients where deployment_id = campaign_schedules.id)
                else
                    (select count(*) from recipients where campaign_id = " . $campaign->id . " and subgroup = recipient_group)
                end as recipients")
            ])
            ->where('campaign_id', $campaign->id)
            ->whereNull('deleted_at');

        if ($request->has('q')) {
            $query->filterByQuery($request->input('q'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        return $query;
    }

    public function scopeFilterByQuery($query, $q)
    {
        return $query->search($q);
    }

    public function getSendAtFormattedAttribute()
    {
        return isset($this->send_at) ? $this->send_at->timezone($this->getUserTimezone())->format("m/d/Y @ g:i A") : '';
    }

    public function getCompletedAtFormattedAttribute()
    {
        return isset($this->completed_at) ? $this->completed_at->timezone($this->getUserTimezone())->format("m/d/Y @ g:i A") : '';
    }

    private function getUserTimezone()
    {
        if (auth()->user() && $company = auth()->user()->getActiveCompany()) {
            return auth()->user()->getTimezone($company);
        }

        return null;
    }
}

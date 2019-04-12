<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sofa\Eloquence\Eloquence;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class CampaignSchedule extends Model implements HasMedia
{
    use SoftDeletes, Eloquence, HasMediaTrait;

    protected $searchableColumns = ['send_at', 'started_at', 'status'];

    public $dates = [
        'send_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $fillable = [
        'type',
        'send_at',
        'email_subject',
        'email_text',
        'email_html',
        'recipient_group',
        'text_message',
        'text_message_image',
        'send_vehicle_image',
        'campaign_id',
        'system_id',
    ];

    protected $appends = ['image_url', 'send_at_formatted', 'sms_phones'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function getSmsPhonesAttribute()
    {
        return $this->campaign->phones()->whereCallSourceName('sms')->count();
    }

    public function recipients()
    {
        return $this->hasMany(Recipient::class, 'campaign_id', 'id');
    }

    public function scopeInGroup($query, $group_id)
    {
        return $query->where('subgroup', $group_id);
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

        return $query;
    }

    public function scopeFilterByQuery($query, $q)
    {
        return $query->search($q);
    }

    /**
     * Accessors
     */
    public function getImageUrlAttribute()
    {
        if ($this->type === 'mailer' && $image = $this->getMedia('image')->last()) {
            return $image->getFullUrl();
        } else {
            return '';
        }
    }

    public function getSendAtFormattedAttribute()
    {
        return $this->send_at ? Carbon::createFromFormat('Y-m-d H:i:s', $this->send_at)->format('Y-m-d g:i A T') : '';
    }
}

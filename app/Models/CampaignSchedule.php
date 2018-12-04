<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignSchedule extends Model
{
    use SoftDeletes;

    public $dates = [
        'send_at', 'created_at', 'updated_at', 'deleted_at'
    ];

    public $fillable = [
        'type', 'send_at', 'email_subject', 'email_text', 'email_html', 'recipient_group',
        'text_message', 'text_message_image', 'send_vehicle_image', 'campaign_id', 'system_id',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function recipients()
    {
        return $this->hasMany(Recipient::class, 'campaign_id', 'id');
    }

    public function scopeInGroup($query, $group_id)
    {
        return $query->where('subgroup', $group_id);
    }
}

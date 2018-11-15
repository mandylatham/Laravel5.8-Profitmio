<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignScheduleTemplate extends Model
{
    use SoftDeletes;

    protected $primaryKey = "campaign_schedule_template_id";

    protected $fillable = [
        'name', 'type', 'email_subject', 'email_text', 'email_html',
        'text_message', 'text_vehicle_image', 'send_vehicle_image'
    ];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhoneNumber extends Model
{
    use SoftDeletes;

    static public $callSources = [
        'email' => 'Email', 
        'mailer' => 'Mailer', 
        'sms' => 'SMS', 
        'text_in' => 'Text-In'
    ];

    protected $table = 'phone_numbers';

    public $fillable = [
        'client_id', 'campaign_id', 'phone_number', 'forward', 'sid', 'region', 'state', 'zip', 'call_source_name'
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }
}

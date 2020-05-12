<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PhoneNumber extends \ProfitMiner\Base\Models\PhoneNumber
{
    use SoftDeletes;

    static public $callSources = [
        'email' => 'Email',
        'mailer' => 'Mailer',
        'sms' => 'SMS'
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function isMailer()
    {
        return $this->call_source_name === 'mailer';
    }

    public function isSms()
    {
        return $this->call_source_name === 'text';
    }
}

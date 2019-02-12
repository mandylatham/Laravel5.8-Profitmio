<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $table = 'email_logs';

    protected $fillable = [
        'message_id',
        'code',
        'campaign_id',
        'recipient_id',
        'event',
        'recipient'
    ];


}

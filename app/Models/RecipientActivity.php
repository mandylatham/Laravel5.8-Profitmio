<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipientActivity extends Model
{
    const OPENED = 'opened';
    const CLOSED = 'closed';
    const VIEWED = 'viewed';
    const REOPENED = 'reopened';
    const SENTSMS = 'sent sms';
    const SENTEMAIL = 'sent email';
    const ADDAPPOINTMENT = 'added appointment';
    const CALLEDBACK = 'logged call back';
    const SENTTOSERVICE = 'sent lead to the service department';
    const SENTTOCRM = 'sent lead to the crm';
    const UPDATEDNOTES = 'updated the notes';

    protected $casts = [
        'action' => 'array',
    ];

    protected $fillable = [
        'action', 'action_by', 'action_at', 'response_id'
    ];

    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }
}

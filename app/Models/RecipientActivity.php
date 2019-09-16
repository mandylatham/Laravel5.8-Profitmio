<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Impersonation\Traits\MayBeImpersonated;

class RecipientActivity extends Model
{
    use MayBeImpersonated;

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

    public $timestamps = false;

    protected $casts = [
        'action' => 'array',
    ];

    protected $fillable = [
        'action', 'action_at', 'response_id', 'user_id'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'recipient_id', 'id');
    }
}

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
    const SENTSMS = 'sent sms';
    const REOPENED = 'reopened';
    const MARKETED = 'sent marketing';
    const SENTEMAIL = 'sent email';
    const SENTTOCRM = 'sent lead to the crm';
    const CALLEDBACK = 'logged call back';
    const UPDATEDNOTES = 'updated the notes';
    const SENTTOSERVICE = 'sent lead to the service department';
    const ADDAPPOINTMENT = 'added appointment';

    public $timestamps = false;

    protected $casts = [
        'metadata' => 'array',
    ];

    protected $dates = ['action_at'];

    protected $fillable = [
        'action', 'action_at', 'response_id', 'user_id', 'metadata',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'recipient_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Impersonation\Traits\MayBeImpersonated;

class Response extends Model
{
    use SoftDeletes, MayBeImpersonated;

    const EMAIL_TYPE = 'email';
    const PHONE_TYPE = 'phone';
    const SMS_TYPE = 'text';
    const TTV_TYPE = 'text-to-value';

	protected $guarded = [];

    protected $appends = ['message_formatted', 'reply_user', 'recording_url'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function recipient()
    {
        return $this->belongsTo(Recipient::class, 'recipient_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sentiment()
    {
        return $this->hasOne(Sentiment::class);
    }

    public function scopeInboundEmail($query)
    {
        return $query->where('type', 'email')->where('incoming', 1);
    }

    /**
     * Accessors
     */
    public function getRecordingUrlAttribute()
    {
        $url = $this->recording_uri;
        if ($this->type === 'phone' && !filter_var($url, FILTER_VALIDATE_URL)) {
            $url = 'https://api.twilio.com/' . $url;
        }

        return $url;
    }

    public function getMessageFormattedAttribute()
    {
        return $this->message ? str_replace('@', '&#64;', $this->message) : '';
    }

    public function getReplyUserAttribute()
    {
        // Provide internal user name
        if (! $this->incoming) {
            if ($this->user) {
                return $this->user->name . ' (id: ' . $this->user->id . ')';
            }
            return $this->campaign->dealership->name;
        }

        // Provide recipient name
        return $this->recipient->name ?? 'Unknown Name';
    }
}

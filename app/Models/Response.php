<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Response extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'read',
        'campaign_id',
        'recipient_id',
        'message',
        'message_id',
        'duration',
        'in_reply_to',
        'subject',
        'type',
        'recording_sid',
        'incoming',
        'call_sid',
        'recording_url',
    ];

    protected $appends = ['created_at_formatted', 'message_formatted'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function recipient()
    {
        return $this->belongsTo(Recipient::class, 'recipient_id', 'id');
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
        $url = $this->attributes['recording_url'];
        if ($this->type === 'phone' && !filter_var($url, FILTER_VALIDATE_URL)) {
            $url = 'https://api.twilio.com/' . $url;
        }

        return $url;
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at ? $this->created_at->timezone(Auth::user()->timezone)->format('Y-m-d g:i A T') . ' ' . ($this->created_at->timezone(Auth::user()->timezone)->diffForHumans()) : '';
    }

    public function getMessageFormattedAttribute()
    {
        return $this->message ? str_replace('@', '&#64;', $this->message) : '';
    }
}

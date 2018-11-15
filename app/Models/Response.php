<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Response extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'response_id';

    protected $fillable = [
        'read', 'campaign_id', 'recipient_id', 'message', 'message_id', 'duration',
        'in_reply_to', 'subject', 'type', 'recording_sid', 'incoming', 'call_sid',
    ];

    public function getIdAttribute()
    {
        return $this->response_id;
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'campaign_id');
    }

    public function scopeInboundEmail($query)
    {
        return $query->where('type', 'email')->where('incoming', 1);
    }
}

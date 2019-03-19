<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneVerification extends Model
{
    const STARTED = 'started';
    const VERIFIED = 'verified';
    const FAILED = 'failed';
    const WAITING = 'waiting';
    
    protected $primaryKey = 'phone';
    public $incrementing = false;
    protected $fillable = ['phone', 'code', 'attempts'];
    public $dates = [
        'started_at', 'verified_at', 'failed_at',
    ];

    public function getStatusAttribute()
    {
        if ($this->failed)  return self::FAILED;
        if ($this->verified) return self::VERIFIED;
        if ($this->started) return self::STARTED;

        return self::WAITING;
    }

    public function getStartedAttribute()
    {
        return !! $this->started_at;
    }

    public function start()
    {
        $this->started_at = \Carbon\Carbon::now('UTC');
        $this->save();
    }

    public function attempt()
    {
        $attempts = $this->attempts;
        $this->attempts = ++$attempts;
        $this->save();
    }

    public function reset()
    {
        $this->attempts = 0;
        $this->failed_at = null;
        $this->failed_reason = null;
        $this->save();
    }

    public function getVerifiedAttribute()
    {
        return !! $this->verified_at;
    }

    public function verify()
    {
        $this->verified_at = \Carbon\Carbon::now('UTC');
        $this->save();
    }

    public function getFailedAttribute()
    {
        return !! $this->failed_at;
    }

    public function fail($reason = null)
    {
        $this->failed_at = now();
        $this->failed_reason = $reason;
        $this->save();
    }

    public function getMessageAttribute()
    {
        if ($this->verified) {
            return;
        }

        return sprintf($this->verificationMessage, $this->code);
    }

}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Lead Model
 *
 * This is the same as a Recipient, except it provides different methods
 */
class Lead extends Recipient
{
    const GOOD_HEALTH = 'ok';
    const WARN_HEALTH = 'warning';
    const POOR_HEALTH = 'past-due';
    const POSITIVE_OUTCOME = 'positive';
    const NEGATIVE_OUTCOME = 'negative';

    protected $fillable = [
        'status',
        'notes',
        'last_status_changed_at',
        'last_responded_at',
        'sent_to_crm',
        'service',
        'interested',
        'not_interested',
        'heat',
        'first_name',
        'last_name',
        'email',
        'phone',
        'make',
        'model',
        'year'
    ];

    public $dates = ['last_status_changed_at', 'last_responded_at'];

    /**
     * Constructor Override.
     *
     * Override to prevent non-responders from being contacted
     */
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            $query->whereHas('responses', function ($q) {
                $q->where('responses.type', Response::EMAIL_TYPE)
                    ->orWhere('responses.type', Response::SMS_TYPE)
                    ->orWhere('responses.type', Response::TTV_TYPE);
            })->orWhereHas('appointments');
        });
    }

    public function checkedIn()
    {
        $textToValue = $this->textToValue;
        if ($textToValue) {
            return $textToValue->checked_in;
        }
        return false;
    }

    public function getCheckedInAt()
    {
        $textToValue = $this->textToValue;
        if ($textToValue) {
            return $textToValue->checked_in_at;
        }
        return '';
    }

    public function textToValueRequested()
    {
        if (!$this->textToValue) {
            return false;
        }
        return $this->textToValue->value_requested;
    }

    public function isClosed()
    {
        return $this->status === self::CLOSED_STATUS;
    }

    // todo: find a way to perform serches in-model

    public function scopeNew($query)
    {
        return $query->whereStatus(Recipient::NEW_STATUS);
    }

    public function scopeOpen($query)
    {
        return $query->whereStatus(Recipient::OPEN_STATUS);
    }

    public function scopeClosed($query)
    {
        return $query->whereStatus(Recipient::CLOSED_STATUS);
    }

    public function scopeHasEmails($query)
    {
        return $query->whereHas('responses', function ($q) {
            $q->where('type', 'email');
        });
    }

    public function scopeHasCalls($query)
    {
        return $query->whereHas('responses', function ($q) {
            $q->where('type', 'phone');
        });
    }

    public function scopeHasSms($query)
    {
        return $query->whereHas('responses', function ($q) {
            $q->where('type', 'text');
        });
    }

    public function open() : void
    {
        $this->update([
            'status' => self::OPEN_STATUS,
            'last_status_changed_at' => now(),
        ]);
    }

    public function close() : void
    {
        $this->update([
            'status' => self::CLOSED_STATUS,
            'last_status_changed_at' => now(),
        ]);
    }

    public function setCheckedIn()
    {
        $textToValue = $this->textToValue;
        if ($textToValue) {
            $textToValue->checked_in = true;
            $textToValue->checked_in_at = Carbon::now()->toDateTimeString();
            $textToValue->save();
        }
    }

    public function reopen()
    {
        $this->update([
            'status' => self::OPEN_STATUS,
            'last_status_changed_at' => now(),
        ]);
    }
}

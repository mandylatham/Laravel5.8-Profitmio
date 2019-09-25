<?php

namespace App\Models;

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

    protected $fillable = ['status', 'notes', 'last_status_changed_at', 'last_responded_at', 'sent_to_crm',
        'service', 'interested', 'not_interested', 'heat'];

    /**
     * Constructor Override.
     *
     * Override to prevent non-responders from being contacted
     */
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            // $query->whereNotNull('last_responded_at');
            $query->has('responses');
        });
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

    public function scopeHealthIs($query, $health)
    {
        return $query;
    }

    public function hasEmails($query)
    {
        return $query->whereHas(['responses' => function ($q) {
            $q->whereType('email');
        }]);
    }

    public function getStatusAttribute($status)
    {
        if ($status === parent::UNMARKETED_STATUS) return 'Uploaded';

        if ($status === parent::MARKETED_STATUS) return 'Contacted';

        if ($status === parent::NEW_STATUS) return 'New';

        if ($status === parent::OPEN_STATUS) return 'Open';

        if ($status === parent::CLOSED_STATUS) return 'Closed';

        return 'ERR';
    }

    /**
     * @param User $user
     */
    public function open() : void
    {
        $this->update([
            'status' => self::OPEN_STATUS,
            'last_status_changed_at' => now(),
        ]);
    }

    /**
     * @param User $user
     */
    public function close(User $user) : void
    {
        $this->update([
            'status' => self::CLOSED_STATUS,
            'last_status_changed_at' => now(),
        ]);
    }

    /**
     * @param User $user
     */
    public function reopen(User $user)
    {
        $this->update([
            'status' => self::OPEN_STATUS,
            'last_status_changed_at' => now(),
        ]);
    }
}

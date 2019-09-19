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

    protected $fillable = ['status', 'notes', 'last_status_changed_at', 'last_responded_at', ];

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

    /**
     * Activity Relationship
     */
    public function activity()
    {
        return $this->hasMany(RecipientActivity::class, 'recipient_id', 'id');
    }

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
        $this->update(['status' => self::OPEN_STATUS]);

        $this->activity()->create([
            'action' => RecipientActivity::OPENED,
            'action_at' => now(),
            'action_by' => auth()->user()
        ]);
    }

    /**
     * @param User $user
     */
    public function close(User $user) : void
    {
        $this->update(['status' => self::CLOSED_STATUS]);

        $this->activity()->create([
            'action' => RecipientActivity::CLOSED,
            'action_at' => now(),
            'action_by' => auth()->user()
        ]);
    }

    /**
     * @param User $user
     */
    public function reopen(User $user)
    {
        $this->update(['status' => self::OPEN_STATUS]);

        $this->activity()->create([
            'action' => RecipientActivity::REOPENED,
            'action_at' => now(),
            'action_by' => auth()->user()
        ]);
    }
}

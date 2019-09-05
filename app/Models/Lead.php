<?php

namespace App\Models;

/**
 * Lead Model
 * 
 * This is the same as a Recipient, except it provides different methods
 */
class Lead extends Recipient
{
    /**
     * Constructor Override.
     * 
     * Override to prevent non-responders from being contacted
     */
    public function __construct(...$args)
    {
        parent::__construct(...$args);
        if (! $this->last_responded_at) {
            throw new \Exception("This recipient has not responded, and therefore is not a lead yet");
        }
    }
}

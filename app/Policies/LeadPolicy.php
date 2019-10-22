<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Lead;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct($user, $ability)
    {
        if ($user->isAdmin())
        {
            return true;
        }
    }

    /**
     * Check if user can update a lead
     *
     * @return bool
     */
    public function update(User $user, Lead $lead) : bool
    {
        $activeCompany = $user->getActiveCompany();
        if ($activeCompany->isDealership()) {
            return $recipient->campaign->dealership_id == $activeCompany->id;
        } else if ($activeCompany->isAgency()) {
            return $recipient->campaign->agency_id == $activeCompany->id;
        }
        return false;
    }
}

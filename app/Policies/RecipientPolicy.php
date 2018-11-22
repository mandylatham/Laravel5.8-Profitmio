<?php

namespace App\Policies;

use App\Models\Recipient;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecipientPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        };
    }

    public function update(User $user, Recipient $recipient)
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

<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\Campaign;
use App\Models\User;

class ResponsePolicy
{
    use HandlesAuthorization;

    public function create(User $user, ?Campaign $campaign): bool
    {
        if ($campaign && $campaign->isExpired()) {
            return false;
        }

        return $user->isAdmin() || $user->isDealershipUser(get_active_company());
    }
}

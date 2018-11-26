<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function list(User $user)
    {
        return $user->isAdmin() || $user->isCompanyAdmin(get_active_company());
    }

    public function resendInvitation(User $user)
    {
        return $user->isAdmin || $user->isCompanyAdmin(get_active_company());
    }

    public function createUser(User $user)
    {
        return $user->isAdmin() || $user->isCompanyAdmin(get_active_company());
    }

    public function impersonate(User $user)
    {
        return auth()->user()->isAdmin();
    }
}

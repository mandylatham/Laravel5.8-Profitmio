<?php

namespace App\Policies;

use App\Models\Company;
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
        return $user->isAdmin() || $user->isCompanyAdmin(get_active_company());
    }

    public function createUser(User $user)
    {
        return $user->isAdmin() || $user->isCompanyAdmin(get_active_company());
    }

    public function editUser(User $loggedUser, User $userToEdit)
    {
        $company = Company::find(get_active_company());
        return $loggedUser->isAdmin() || (
            $loggedUser->isCompanyAdmin(get_active_company()) && $userToEdit->belongsToCompany($company)
            ) || $loggedUser->id == $userToEdit->id;
    }

    public function deleteUser(User $loggedUser, User $userToDelete)
    {
        return $loggedUser->isAdmin() && !$userToDelete->isAdmin();
    }

    public function impersonate(User $user)
    {
        return auth()->user()->isAdmin();
    }

    public function siteAdmin(User $user)
    {
        return $user->isAdmin();
    }
}

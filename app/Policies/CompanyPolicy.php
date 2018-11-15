<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Company;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the company.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return mixed
     */
    public function view(User $user, Company $company)
    {
        $company = $user->companies->find($company->id);

        if (is_null($company->pivot->completed_at)) {
            redirect(route('companies.preferences', ['company' => $company->id]))->send();
        }
        return ($user->isCompanyAdmin($company->id) || $user->isCompanyUser($company->id));
    }

    /**
     * Determine whether the user can view the company.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return mixed
     */
    public function viewForPreferences(User $user, Company $company)
    {
        $company = $user->companies->find($company->id);

        return ($user->isCompanyAdmin($company->id) || $user->isCompanyUser($company->id));
    }

    /**
     * Determine whether the user can create companies.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the company.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return mixed
     */
    public function update(User $user, Company $company)
    {
        //
    }

    /**
     * Determine whether the user can delete the company.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return mixed
     */
    public function delete(User $user, Company $company)
    {
        //
    }

    /**
     * Determine whether the user can restore the company.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return mixed
     */
    public function restore(User $user, Company $company)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the company.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return mixed
     */
    public function forceDelete(User $user, Company $company)
    {
        //
    }

    public function manage(User $user, Company $company)
    {
        return $user->isCompanyAdmin($company->id);
    }
}

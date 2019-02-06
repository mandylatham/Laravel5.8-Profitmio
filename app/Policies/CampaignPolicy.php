<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Campaign;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampaignPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        };
    }

    public function list(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the campaign.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Campaign  $campaign
     * @return mixed
     */
    public function view(User $user, Campaign $campaign)
    {
        $activeCompany = $user->getActiveCompany();
        if ($activeCompany->isAgency()) {
            return $campaign->agency_id == $activeCompany->id;
        } else if ($activeCompany->isDealership()) {
            return $campaign->dealership_id == $activeCompany->id;
        }
        return false;
    }

    /**
     * Determine whether the user can create campaigns.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the campaign.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Campaign  $campaign
     * @return mixed
     */
    public function update(User $user, Campaign $campaign)
    {
        //
    }

    /**
     * Determine whether the user can delete the campaign.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Campaign  $campaign
     * @return mixed
     */
    public function delete(User $user, Campaign $campaign)
    {
        //
    }

    /**
     * Determine whether the user can restore the campaign.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Campaign  $campaign
     * @return mixed
     */
    public function restore(User $user, Campaign $campaign)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the campaign.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Campaign  $campaign
     * @return mixed
     */
    public function forceDelete(User $user, Campaign $campaign)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the campaign.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Campaign  $campaign
     * @return mixed
     */
    public function manage(User $user, Campaign $campaign)
    {
        // user is admin of dealership or agency
        return ($user->isCompanyAdmin($campaign->agency->id) || $user->isCompanyAdmin($campaign->dealership->id));
    }

}

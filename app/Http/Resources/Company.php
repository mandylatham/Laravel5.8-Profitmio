<?php

namespace App\Http\Resources;

use App\Models\User as UserModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Company extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'address' => $this->address,
            'created_at' => (string)$this->created_at,
            'active_campaigns' => $this->when(auth()->user()->isAdmin() || auth()->user()->isCompanyAdmin($this->id), $this->activeCampaigns()->count()),
            'is_active' => $this->whenPivotLoaded('company_user', function () {
                return (bool) $this->pivot->is_active;
            }),
            'is_profile_ready' => $this->whenPivotLoaded('company_user', function () {
                return $this->isUserProfileReady($this->pivot->user_id);
            }),
        ];
        // Verify if role field should be added
        if ($this->userCanSeeRolesAndTimezone($request)) {
            $userModel = UserModel::findOrFail($request->input('user'));
            $data['role'] = $userModel->getRole($this->resource);
            $data['timezone'] = $userModel->getTimezone($this->resource);
            $data['active_campaigns_for_user'] = count($userModel->getActiveCampaignsForCompany($this->resource));
            $data['is_profile_ready'] = $this->isUserProfileReady($userModel->id);
        }
        $this->whenPivotLoaded('company_user', function () use (&$data) {
            $data['role'] = $this->pivot->role;
        });
        return $data;
    }

    private function userCanSeeRolesAndTimezone(Request $request)
    {
        $user_id = $request->input('user');
        return $user_id && (
            // Is the operator a Site Admin?
            auth()->user()->isAdmin() || 

            // Is the operator a Company Admin for the company?
            auth()->user()->isCompanyAdmin($this->id) || 

            // Is the operator looking at their own Profile?
            auth()->user()->id === (int)$user_id
        );
    }
}

<?php

namespace App\Http\Resources;

use App\Models\CampaignUser;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'is_admin' => (int) $this->is_admin,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'is_profile_ready' => $this->when($request->filled('company') && auth()->user()->isAdmin(), function () use ($request) {
                $company = \App\Models\Company::find($request->input('company'));
                return $company->isUserProfileReady($this->id);
            }),
            'has_access' => $this->when($request->filled('campaign') && $request->filled('company'), function () use ($request) {
                $company = \App\Models\Company::find($request->input('company'));
                if ($this->resource->isAdmin() || $this->resource->isCompanyAdmin($company->id)) {
                    return true;
                }
                return CampaignUser::where('user_id', $this->id)->where('campaign_id', $request->input('campaign'))->count() > 0;
            }),
            'is_active' => $this->when(!auth()->user()->isAdmin(), function () {
                return (bool) $this->resource->isActive(get_active_company());
            }),
            'role' => $this->when(!auth()->user()->isAdmin(), function () {
                return $this->resource->getRole(\App\Models\Company::findOrFail(get_active_company()));
            }),
            'active_companies' => $this->countActiveCompanies(),
            'has_active_companies' => $this->hasActiveCompanies(),
            'has_pending_invitations' => $this->hasPendingInvitations()
        ];
        if (!auth()->user()->isAdmin()) {
            $data['is_active'] = (bool) $this->resource->isActive(get_active_company());
        } else if (auth()->user()->isAdmin() && $request->filled('company')) {
            $data['is_active'] = (bool) $this->resource->isActive($request->input('company'));
        }
        return $data;
    }
}

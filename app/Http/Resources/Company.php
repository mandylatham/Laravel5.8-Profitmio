<?php

namespace App\Http\Resources;

use App\Models\User as UserModel;
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
            'address' => $this->address,
            'active_campaigns' => $this->when(auth()->user()->isAdmin() || auth()->user()->isCompanyAdmin($this->id), $this->activeCampaigns()->count()),
            'is_active' => $this->whenPivotLoaded('company_user', function () {
                return (bool) $this->pivot->is_active;
            }),
            'is_profile_ready' => $this->whenPivotLoaded('company_user', function () {
                return $this->isUserProfileReady($this->pivot->user_id);
            }),
        ];
        // Verify if role field should be added
        if ((auth()->user()->isAdmin() || auth()->user()->isCompanyAdmin($this->id)) && $request->has('user')) {
            $userModel = UserModel::findOrFail($request->input('user'));
            $data['role'] = $userModel->getRole($this->resource);
            $data['timezone'] = $userModel->getTimezone($this->resource);
        }
        $this->whenPivotLoaded('company_user', function () use (&$data) {
            $data['role'] = $this->pivot->role;
        });
        return $data;
    }
}

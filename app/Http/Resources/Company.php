<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->whenPivotLoaded('company_user', function () {
                return $this->pivot->role;
            }),
            'is_active' => $this->whenPivotLoaded('company_user', function () {
                return (bool) $this->pivot->is_active;
            }),
            'is_profile_ready' => $this->whenPivotLoaded('company_user', function () {
                return $this->isUserProfileReady($this->pivot->user_id);
            }),
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Lead as LeadModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Lead extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $last_change = $this->last_status_changed_at;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'labels' => $this->labels,
            'last_responded_at' => $this->last_responded_at,
            'status' => $this->status_for_humans,
            'secondsLeft' => $this->last_status_changed_at->diffInSeconds(now()),
        ];
    }
}

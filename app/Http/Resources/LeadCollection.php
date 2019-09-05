<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LeadCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address1' => $this->address1,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'vehicle' => $this->vehicle,
            'vin' => $this->vin,
            'dropped_at' => $this->getDroppedTime()
        ];
    }
}

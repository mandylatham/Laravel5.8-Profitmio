<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecipientList extends JsonResource
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
            'error' => !$this->failed_at ? null : [
                'message' => $this->failed_reason,
                'time' => $this->failed_at->getTimestamp(),
            ],
            'recipient_count' => $this->recipients()->count(),
            'with_email' => $this->withEmails(),
            'with_phone' => $this->withPhones(),
            'from_conquest' => $this->fromConquest(false),
            'from_dealer' => $this->fromDealerDb(true)
        ];
    }
}

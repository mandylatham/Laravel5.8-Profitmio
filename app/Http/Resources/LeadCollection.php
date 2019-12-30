<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LeadCollection extends ResourceCollection
{
    public $collects = Lead::class;

    /**
     * Transform the resource collection into an array.
     * ::collection
     *
     * @param  \Illuminate\Http\Request $request
     * @return areturnrray
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'links' => [
                'self' => 'link-value',
            ]
        ];
    }
}

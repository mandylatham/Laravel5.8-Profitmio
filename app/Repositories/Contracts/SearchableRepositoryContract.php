<?php
namespace App\Repositories\Contracts;

use App\Models\Campaign;
use Illuminate\Http\Request;

interface SearchableRepositoryContract
{
    /**
     * Search by Request
     *
     * @param Request $request
     */
    public function byRequest(Request $request);

    /**
     * Set the campaign
     *
     * @param Campaign $campaign
     */
    public function forCampaign(Campaign $campaign);
}

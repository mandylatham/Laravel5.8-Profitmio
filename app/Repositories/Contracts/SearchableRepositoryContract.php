<?php
namespace App\Repositories\Contracts;

use App\Models\Campaign;
use Illuminate\Http\Request;

interface SearchableRepositoryContract
{
    /**
     * Search by Request
     *
     * @param mixed $id
     *
     * @return Model
     */
    public function byRequest(Campaign $campaign, Request $request);
}

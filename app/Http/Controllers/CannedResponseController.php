<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCannedResponseRequest;
use App\Models\CannedResponse;
use App\Models\Lead;
use App\Models\LeadTag;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LeadTagRequest;
use App\Http\Resources\LeadTag as LeadTagResource;

class CannedResponseController extends Controller
{
    public function store(Campaign $campaign, StoreCannedResponseRequest $request)
    {
        return $campaign->cannedResponses()->create($request->toArray());
    }

    public function delete(Campaign $campaign, CannedResponse $cannedResponse)
    {
        $response = $campaign->cannedResponses()->where('canned_response.id', $cannedResponse->id)->firstOrFail();
        $response->delete();

        return response()->json([]);
    }
}

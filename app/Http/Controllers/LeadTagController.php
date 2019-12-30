<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadTag;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LeadTagRequest;
use App\Http\Resources\LeadTag as LeadTagResource;

class LeadTagController extends Controller
{
    private $tags;
    private $leads;

    /**
     * Constructor.
     *
     * @param Lead $lead
     * @param LeadTag $tag
     */
    public function __construct(Lead $leads, LeadTag $tags)
    {
        $this->tags = $tags;
        $this->leads = $leads;
    }

    /**
     * Get resource index
     *
     * @param Campaign $campaign
     *
     * @return LeadTagResource
     */
    public function index(Campaign $campaign)
    {
        return LeadTagResource::collection($campaign->tags);
    }

    /**
     * Store a new LeadTag resource
     *
     * @param Campaign $campaign
     * @param LeadTagRequest $request
     *
     * @return void
     */
    public function store(Campaign $campaign, LeadTagRequest $request)
    {
        if ($campaign->tags()->whereName($request->input('name'))->count()) {
            abort(500, "Duplicate tag, " . $request->input('name'));
        }

        try {
            $campaign->tags()->create($request->toArray());
        } catch (\Exception $e) {
            abort(500, "Unknown error");
        }
    }

    /**
     * Destroy an existing LeadTag resource
     *
     * @param Campaign $campaign
     * @param string $tag
     *
     * @return void
     */
    public function destroy(Campaign $campaign, string $tag)
    {
        $campaign->tags()->whereName($tag)->firstOrFail();

        // Workaround for no-id error
        $deleted = DB::table('lead_tags')
            ->whereCampaignId($campaign->id)
            ->whereName($tag)
            ->delete();

        return response()->json(['tag' => $tag, 'was_deleted' => $deleted]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Company;
use App\Models\Drop;
use Illuminate\Http\Request;

/**
 * Appointment Controller
 */
class DropController extends Controller
{
    private $drop;

    private $company;

    private $campaign;

    public function __construct(Drop $drop, Campaign $campaign, Company $company)
    {
        $this->company = $company;
        $this->drop = $drop;
        $this->campaign = $campaign;
    }

    public function getCampaignIds()
    {
        $ids = $this->campaign->select('id');
        $company = $this->company->findOrFail(get_active_company());

        if ($company->isDealership()) {
            $ids->where('dealership_id', $company->id);
        } else if ($company->isAgency()) {
            $ids->where('agency_id', $company->id);
        }

        return $ids->get()->toArray();
    }

    public function getForCalendarDisplay(Request $request)
    {
        $ids = $this->getCampaignIds();

        // Get the Campaigns
        $drops = $this->drop->whereIn('campaign_id', $ids)
            ->whereNull('deleted_at')
            ->selectRaw("
				concat('Campaign ', campaign_id, ': ', type, ' drop') as title, send_at as start")
            ->get();
        $drops = $drops->map(function ($item) {
            return [
                'title' => $item->title,
                'start' => $item->send_at->toDateTimeString(),
                'date' => $item->send_at->toDateString()
            ];
        });

        return $drops;
    }

}

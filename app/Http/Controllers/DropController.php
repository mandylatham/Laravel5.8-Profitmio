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
        $ids = $this->campaign->select('campaigns.id');
        $company = $this->company->findOrFail(get_active_company());
        $loggedUser = auth()->user();

        if ($company->isDealership()) {
            $ids->where('dealership_id', $company->id);
        } else if ($company->isAgency()) {
            $ids->where('agency_id', $company->id);
        }

        if (!$loggedUser->isCompanyAdmin($company->id)) {
            $ids->join('campaign_user', 'campaign_user.campaign_id', '=', 'campaigns.id')
                ->where('campaign_user.user_id', $loggedUser->id);
        }

        return $ids->get()->pluck('id')->toArray();
    }

    public function getForCalendarDisplay(Request $request)
    {
        $ids = $this->getCampaignIds();

        // Get the Campaigns
        $drops = $this->drop->whereIn('campaign_id', $ids)
            ->select('campaign_schedules.*')
            ->whereNull('deleted_at');

        if ($request->has('start_date')) {
            $drops->whereDate('send_at', '>=', $request->input('start_date'));
        }

        if ($request->has('end_date')) {
            $drops->whereDate('send_at', '<=', $request->input('end_date'));
        }
        return $drops->paginate($request->input('per_page', 50));
    }

}

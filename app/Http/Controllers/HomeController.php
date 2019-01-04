<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Campaign;
use App\Models\Company;
use App\Models\Response;

class HomeController extends Controller
{
    private $campaign;

    private $company;

    public function __construct(Campaign $campaign, Company $company)
    {
        $this->middleware('auth');

        $this->campaign = $campaign;
        $this->company = $company;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('campaign.index');
        }
//        $ids = $this->getCampaignIds();
//        $activeCompany = $this->company->findOrFail(get_active_company());
//
//        // Get the Campaigns
//        $allinone = $this->campaign
//            ->leftJoin('responses', 'campaigns.id', '=', 'responses.campaign_id')
//            ->leftJoin('companies', 'companies.id', '=', 'campaigns.dealership_id')
//            ->selectRaw(
//                "campaigns.id as id, campaigns.name, campaigns.status,
//                companies.name as company_name,
//                count(distinct responses.recipient_id) as responders,
//                count(distinct(case when responses.read = 0 and responses.incoming = 1 and responses.type <> 'phone' then recipient_id end)) as unread,
//                count(distinct(case when responses.type = 'phone' and responses.incoming = 1 then recipient_id end)) as phones,
//                count(distinct(case when responses.type = 'email' and responses.incoming = 1 then recipient_id end)) as emails,
//                count(distinct(case when responses.type = 'text' and responses.incoming = 1 then recipient_id end)) as texts
//            ")
//            ->groupBy('campaigns.id');
//
//        // Get Appointment Counts
//        $appointmentCounts = \DB::table('appointments')
//            ->selectRaw(
//                "campaign_id,
//                sum(case when appointments.type = 'appointment' then 1 else 0 end) as appointments,
//                sum(case when appointments.type = 'callback' then 1 else 0 end) as callbacks")
//            ->whereNull('deleted_at');
//
//        // Get Callbacks
//        $callbacks = Appointment::where('type', 'callback')
//            ->where('called_back', false);
//
//        if ($activeCompany->isAgency() || $activeCompany->isDealership()) {
//            $callbacks->whereIn('campaign_id', $ids);
//
//            $appointmentCounts->whereIn('campaign_id', $ids);
//        }
//
//        // Get Appointments for Calendar
//        $appointments = \DB::table('appointments')
//            ->where('called_back', 0)
//            ->whereIn('campaign_id', $ids)
//            ->where('type', 'appointment')
//            ->whereNotNull('appointment_at')
//            ->selectRaw("concat('Campaign ', campaign_id, ': ', first_name,' ',last_name,': ',phone_number) as title, appointment_at as start")
//            ->get();
//
//        // Only allow authorized campaigns
//        $allinone = $allinone->whereIn('campaigns.id', $ids)
//            ->groupBy([
//                'campaigns.id',
//                'campaigns.name',
//                'campaigns.status',
//                "company_name",
//            ])
//            ->orderBy('campaigns.id', 'desc')
//            ->get()
//            ->keyBy('id');
//
//        // Get Drops for Calendar
//        $drops = \App\Models\Drop::whereIn('campaign_id', $ids)
//            ->whereNull('deleted_at')
//            ->selectRaw("
//				concat('Campaign ', campaign_id, ': ', type, ' drop') as title, send_at")
//            ->get();
//
//        $drops = $drops->map(function ($item, $key) {
//            return ['title' => $item->title, 'start' => $item->send_at->toDateTimeString()];
//        });
//
//        $viewData['campaigns'] = $allinone;
//        $viewData['appointmentCounts'] = $appointmentCounts->groupBy('campaign_id')->get()->keyBy('campaign_id');
//        $viewData['stats'] = $this->getStats($ids);
//        $viewData['callbacks'] = $callbacks->whereIn('campaign_id', $ids)->get();
//        $viewData['appointments'] = $appointments->toJson();
//        $viewData['drops'] = $drops->toJson();

        return view('dashboard.index', [
            'q' => session('filters.dashboard.q')
        ]);
    }

    public function lightDashboard()
    {
        $ids = $this->getCampaignIds();

        // Get Callbacks
        $callbacks = Appointment::where('type', 'callback')
            ->where('called_back', false);

        $viewData['callbacks'] = $callbacks->whereIn('campaign_id', $ids)->get();
        $viewData['stats'] = $this->getStats($ids);

        return view('dashboard.index', $viewData);
    }

    public function getAppointments()
    {

    }

    protected function getStats($ids)
    {
        $stats = [
            'responses' => Response::where('incoming', 1),
            'calls' => Response::where('type', 'phone')->where('incoming', 1),
            'sms' => Response::where('type', 'text')->where('incoming', 1),
            'emails' => Response::where('type', 'email')->where('incoming', 1),
            'appointments' => Appointment::where('type', 'appointment'),
            'callbacks' => Appointment::where('type', 'callback'),
        ];

        foreach ($stats as &$stat) {
            $stat->whereIn('campaign_id', $ids);
        }

        foreach ($stats as &$stat) {
            $stat = $stat->count();
        }

        return (object) $stats;
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
}

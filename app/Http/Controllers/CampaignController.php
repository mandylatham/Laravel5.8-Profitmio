<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Company;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests\NewCampaignRequest;
use Carbon\Carbon;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $campaigns = Campaign::query()
            ->withCount(['recipients', 'email_responses', 'phone_responses', 'text_responses'])
            ->with(['dealership', 'agency'])
            ->whereNull('deleted_at')
            ->whereIn('status', ['Active', 'Completed', 'Upcoming']);

        if ($request->has('q')) {
            $likeQ = '%' . $request->get('q') . '%';
            $campaigns->where('name', 'like', $likeQ)
                ->orWhere('id', 'like', $likeQ)
                ->orWhere('starts_at', 'like', $likeQ)
                ->orWhere('ends_at', 'like', $likeQ)
                ->orWhere('order_id', 'like', $likeQ);
        }

        $campaigns = $campaigns->orderBy('campaigns.id', 'desc')
            ->paginate(15);

        return view('campaigns.index', ['campaigns' => $campaigns]);
    }

    public function getList(Request $request)
    {
        $valid_filters = ['first_name', 'last_name', 'email', 'phone', 'year', 'make', 'model', 'vin', 'address1', 'city', 'state', 'zip'];
        $filters = [];

        $campaigns = Campaign::with('client')
            ->select(\DB::raw("(select count(distinct(recipient_id)) from recipients where campaign_id = campaigns.id) as recipientCount,)"))
            ->select(\DB::raw("(select count(distinct(recipient_id)) from responses where campaign_id = campaigns.id and type='phone' and recording_sid is not null) as phoneCount,"))
            ->select(\DB::raw("(select count(distinct(recipient_id)) from responses where campaign_id = campaigns.id and type='email') as emailCount,"))
            ->select(\DB::raw("(select count(distinct(recipient_id)) from responses where campaign_id = campaigns.id and type='text') as textCount,"))
            ->select(\DB::raw("users.id as client_id"))
            ->get();

        return $campaigns->toJson();
    }

    /**
     * Show a specific campaign
     *
     * @param \App\Models\Campaign $campaign
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Campaign $campaign)
    {
        $viewData = [];

        $viewData['campaign'] = $campaign;

        $emailStats = DB::table('email_logs')
            ->select(
                DB::raw("sum(if(event = 'sent', 1, 0)) as sent"),
                DB::raw("sum(if(event = 'delivered', 1, 0)) as delivered"),
                DB::raw("sum(if(event = 'opened', 1, 0)) as opened"),
                DB::raw("sum(if(event = 'clicked', 1, 0)) as clicked"),
                DB::raw("sum(if(event = 'bounced', 1, 0)) as bounced"),
                DB::raw("sum(if(event = 'dropped', 1, 0)) as dropped"),
                DB::raw("sum(if(event = 'unsubscribed', 1, 0)) as unsubscribed"),
                DB::raw("count(*) as total"))
            ->where('campaign_id', $campaign->id)
            ->get();

        if ($emailStats->count() > 0 && $emailStats->first()->sent > 0) {
            $emailObject = $emailStats->first();
            $emailObject->droppedPercent = round(abs((($emailObject->sent -
                        ($emailObject->dropped)) / $emailObject->sent * 100) - 100), 2);

            $emailObject->bouncedPercent = round(abs((($emailObject->sent -
                        $emailObject->bounced) / $emailObject->sent * 100) - 100), 2);
            $emailStats = new Collection([$emailObject]);
        }

        $responseStats = DB::table('recipients')
            ->select(
                DB::raw("sum(service) as service"),
                DB::raw("sum(appointment) as appointment"),
                DB::raw("sum(heat) as heat"),
                DB::raw("sum(interested) as interested"),
                DB::raw("sum(not_interested) as not_interested"),
                DB::raw("sum(wrong_number) as wrong_number"),
                DB::raw("sum(car_sold) as car_sold"),
                DB::raw("count(*) as total"))
            ->where('campaign_id', $campaign->id)
            ->get();

        $viewData['emailCount'] = $emailStats->count();
        $viewData['emailStats'] = $emailStats->first();
        $viewData['responseCount'] = $responseStats->count();
        $viewData['responseStats'] = $responseStats->first();

        return view('campaigns.dashboard', $viewData);
    }

    public function details(Campaign $campaign)
    {
        $allCampaignData = Campaign::where('campaign_id', $campaign->id)
            ->with('client', 'agency', 'schedules', 'phone_number')
            ->get();
        $viewData['campaign'] = $allCampaignData->first();

        return view('campaigns.details', $viewData);
    }

    public function createNew()
    {
        $dealerships = Company::getDealerships();
        $agencies = Company::getAgencies();
        $viewData = [
            'dealerships' => $dealerships,
            'agencies' => $agencies,
        ];

        return view('campaigns.new', $viewData);
    }

    public function create(NewCampaignRequest $request)
    {
        $campaign = new Campaign([
            'name' => $request->input('name'),
            'status' => $request->input('status'),
            'order_id' => $request->input('order'),
            'starts_at' => (new Carbon($request->input('start'), auth()->user()->timezone))->timezone('UTC')->toDateTimeString(),
            'ends_at' => (new Carbon($request->input('end'), auth()->user()->timezone))->timezone('UTC')->toDateTimeString(),
            'agency_id' => $request->input('agency'),
            'dealership_id' => $request->input('client'),
            'adf_crm_export' => (bool) $request->input('adf_crm_export'),
            'adf_crm_export_email' => $request->input('adf_crm_export_email'),
            'lead_alerts' => (bool) $request->input('lead_alerts'),
            'lead_alert_email' => $request->input('lead_alert_email'),
            'client_passthrough' => (bool) $request->input('client_passthrough'),
            'client_passthrough_email' => $request->input('client_passthrough_email'),
            'phone_number_id' => $request->input('phone_number_id'),
        ]);

        $campaign->save();

        return redirect()->route('campaign.index');
    }


    public function edit(Campaign $campaign)
    {
        $dealerships = Company::getDealerships();
        $agencies = Company::getAgencies();
        $campaign->load("phone");
        $viewData = [
            'campaign' => $campaign,
            'dealerships' => $dealerships,
            'agencies' => $agencies,
        ];

        return view('campaigns.edit', $viewData);
    }

    public function update(Campaign $campaign, NewCampaignRequest $request)
    {
        if ($request->filled('phone_number_id') || $request->filled('forward')) {
            $phone = \App\Models\PhoneNumber::findOrFail($request->phone_number_id);
            $phone->fill(['forward' => $request->forward]);
            $phone->save();
        }

        $campaign->fill([
            'name' => $request->name,
            'status' => $request->status,
            'order_id' => $request->order,
            'starts_at' => (new Carbon($request->start, \Auth::user()->timezone))->timezone('UTC')->toDateTimeString(),
            'ends_at' => (new Carbon($request->end, \Auth::user()->timezone))->timezone('UTC')->toDateTimeString(),
            'agency_id' => $request->agency,
            'dealership_id' => $request->client,
            'adf_crm_export' => (bool) $request->adf_crm_export,
            'adf_crm_export_email' => $request->adf_crm_export_email,
            'lead_alerts' => (bool) $request->lead_alerts,
            'lead_alert_email' => $request->lead_alert_email,
            'client_passthrough' => (bool) $request->client_passthrough,
            'client_passthrough_email' => $request->client_passthrough_email,
            'phone_number_id' => $request->phone_number_id,
        ]);

        if ($request->has('forward')) {
            $campaign->phone->forward = $request->forward;
        }

        $campaign->save();

        return redirect()->route('campaign.edit', ['campaign' => $campaign->id]);
    }

    public function delete(Campaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('campaign.index');
    }
//
//    /**
//     * Display a listing of the resource.
//     *
//     * @return \Illuminate\Http\Response
//     */
//    public function index()
//    {
//        $campaigns = Campaign::all();
//        return view('campaign/index', ['campaigns' => $campaigns]);
//    }
//
//    /**
//     * Show the form for creating a new resource.
//     *
//     * @return \Illuminate\Http\Response
//     */
//    public function create()
//    {
//        $agencies = Company::getAgencies();
//        $dealerships = Company::getDealerships();
//        return view('campaign/create', ['agencies' => $agencies, 'dealerships' => $dealerships]);
//    }
//
//    /**
//     * Store a newly created resource in storage.
//     *
//     * @param  \Illuminate\Http\Request  $request
//     * @return \Illuminate\Http\Response
//     */
//    public function store(Request $request)
//    {
//        Campaign::create($request->only(['name', 'agency_id', 'dealership_id']));
//        return response()->redirectToRoute('campaigns.index');
//    }
//
//    /**
//     * Display the specified resource.
//     *
//     * @param  \App\Models\Company  $company
//     * @return \Illuminate\Http\Response
//     */
//    public function show(Company $company)
//    {
//    }
//
//    /**
//     * Show the form for editing the specified resource.
//     *
//     * @param  \App\Models\Company  $company
//     * @return \Illuminate\Http\Response
//     */
//    public function edit(Company $company)
//    {
//        return view('company/edit', ['company' => $company]);
//    }
//
//    /**
//     * Update the specified resource in storage.
//     *
//     * @param  \Illuminate\Http\Request  $request
//     * @param  \App\Models\Company  $company
//     * @return \Illuminate\Http\Response
//     */
//    public function update(Request $request, Company $company)
//    {
//        $company->update($request->only(['name', 'type']));
//        return response()->redirectToRoute('companies.index');
//    }
//
//    /**
//     * Remove the specified resource from storage.
//     *
//     * @param  \App\Models\Company  $company
//     * @return \Illuminate\Http\Response
//     */
//    public function destroy(Company $company)
//    {
//        //
//    }
}

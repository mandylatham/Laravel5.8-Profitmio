<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Company;
use App\Models\EmailLog;
use App\Models\Recipient;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests\NewCampaignRequest;
use Carbon\Carbon;

class CampaignController extends Controller
{
    private $emailLog;

    private $campaign;

    private $company;

    private $recipient;

    public function __construct(Campaign $campaign, Company $company, EmailLog $emailLog, Recipient $recipient)
    {
        $this->campaign = $campaign;
        $this->company = $company;
        $this->emailLog = $emailLog;
        $this->recipient = $recipient;
    }

    public function index(Request $request)
    {
        return view('campaign.index', [
            'companySelected' => $this->company->find(session('filters.campaign.index.company')),
            'q' => session('filters.campaign.index.q')
        ]);
    }

    /**
     * Return all campaigns for user display
     * @param Request $request
     * @return mixed
     */
    public function getForUserDisplay(Request $request)
    {
        $campaignQuery = Campaign::searchByRequest($request);
        $campaigns = $campaignQuery
            ->orderBy('status', 'asc')
            ->orderBy('campaigns.id', 'desc')
            ->paginate(15);

        return $campaigns;
    }

    public function getList(Request $request)
    {
        $campaigns = $this->campaign->with(['client', 'mailers'])
            ->selectRaw("
                (select count(distinct(recipient_id)) from recipients where campaign_id = campaigns.id) as recipientCount),
                (select count(distinct(recipient_id)) from responses where campaign_id = campaigns.id and type='phone' and recording_sid is not null) as phoneCount,
                (select count(distinct(recipient_id)) from responses where campaign_id = campaigns.id and type='email') as emailCount,
                (select count(distinct(recipient_id)) from responses where campaign_id = campaigns.id and type='text') as textCount,
                users.id as client_id
            ")
            ->get();

        return $campaigns->toJson();
    }

    public function addMailer(Request $request)
    {
        $mailer = $this->campaign->mailers()->create([
            'name' => $request->mailer_name,
            'in_home_ap' => $request->in_home_date,
        ]);

        $mailer->addMedia($request->file('mailer_image'));
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

        $emailStats = $campaign->getEmailLogsStats();
        $responseStats = $campaign->getRecipientStats();

        $viewData['emailCount'] = $emailStats->count();
        $viewData['emailStats'] = $emailStats->first();
        $viewData['responseCount'] = $responseStats->count();
        $viewData['responseStats'] = $responseStats->first();

        return view('campaigns.dashboard', $viewData);
    }

    public function details(Campaign $campaign)
    {
        return view('campaigns.details', [
            'campaign' => $campaign->load('client', 'agency', 'schedules', 'phone_number')
        ]);
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
        $expires_at = null;
        $starts_at = (new Carbon($request->start, \Auth::user()->timezone))->timezone('UTC')->toDateTimeString();
        $ends_at = (new Carbon($request->end, \Auth::user()->timezone))->timezone('UTC')->toDateTimeString();

        if (! empty($request->input('expires'))) {
            $expires_at = (new Carbon($request->expires, \Auth::user()->timezone))->timezone('UTC')->toDateTimeString();
        } else {
            $expires_at = (new Carbon($request->end, \Auth::user()->timezone))->timezone('UTC')->addWeeks(2);
        }

        $status = $request->status;
        if ($expires_at <= \Carbon\Carbon::now('UTC')) {
            $status = 'Expired';
        }
        $campaign = new $this->campaign([
            'name' => $request->input('name'),
            'status' => $status,
            'order_id' => $request->input('order'),
            /**
             * TODO: Get correct timezone (now user doesn't have a timezone, timezone is stored in company_user table)
             */
            'starts_at' => $starts_at,
            'ends_at' => $ends_at,
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

        if (! $campaign->expires_at) {
            $campaign->expires_at = $campaign->ends_at->addMonth();
        }

        $campaign->save();

        if ($campaign->phone) {
            $phone = $campaign->phone;
            $phone->campaign_id = $campaign->id;
            $phone->save();
        }

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

        $expires_at = null;
        $starts_at = (new Carbon($request->start, \Auth::user()->timezone))->timezone('UTC')->toDateTimeString();
        $ends_at = (new Carbon($request->end, \Auth::user()->timezone))->timezone('UTC')->toDateTimeString();

        if (! empty($request->input('expires'))) {
            $expires_at = (new Carbon($request->expires, \Auth::user()->timezone))->timezone('UTC')->toDateTimeString();
        } else {
            $expires_at = (new Carbon($request->end, \Auth::user()->timezone))->timezone('UTC')->addWeeks(2);
        }

        $status = $request->status;
        if (! $expires_at || ($expires_at && $expires_at <= \Carbon\Carbon::now('UTC'))) {
            $status = 'Expired';
        }

        $campaign->fill([
            'name' => $request->name,
            'status' => $status,
            'order_id' => $request->order,
            'starts_at' => $starts_at,
            'ends_at' => $ends_at,
            'expires_at' => $expires_at,
            'agency_id' => $request->agency,
            'dealership_id' => $request->client,
            'adf_crm_export' => (bool) $request->adf_crm_export,
            'adf_crm_export_email' => $request->adf_crm_export_email,
            'lead_alerts' => (bool) $request->lead_alerts,
            'lead_alert_email' => $request->lead_alert_email,
            'client_passthrough' => (bool) $request->client_passthrough,
            'client_passthrough_email' => $request->client_passthrough_email,
            'service_dept' => (bool) $request->service_dept,
            'service_dept_email' => $request->service_dept_email,
            'sms_on_callback' => (bool) $request->sms_on_callback,
            'sms_on_callback_number' => $request->sms_on_callback_number,
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
}

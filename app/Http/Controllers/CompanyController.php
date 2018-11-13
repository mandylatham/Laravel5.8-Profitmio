<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\CampaignUser;
use App\Classes\CampaignUserActivityLog;
use App\Classes\CompanyUserActivityLog;
use App\Company;
use App\CompanyUser;
use App\Mail\InviteUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class CompanyController extends Controller
{
    /** @var CompanyUserActivityLog  */
    private $companyUserActivityLog;

    /** @var CampaignUserActivityLog  */
    private $campaignUserActivityLog;

    public function __construct(CompanyUserActivityLog $companyUserActivityLog, CampaignUserActivityLog $campaignUserActivityLog)
    {
        $this->companyUserActivityLog = $companyUserActivityLog;
        $this->campaignUserActivityLog = $campaignUserActivityLog;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::all();
        return view('company/index', ['companies' => $companies]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('company/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Company::create($request->only(['name', 'type']));
        return response()->redirectToRoute('companies.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        return view('company/edit', ['company' => $company]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $company->update($request->only(['name', 'type']));
        return response()->redirectToRoute('companies.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        //
    }

    public function dashboard(Company $company)
    {
        /** @var User $user */
        $user = Auth::user();
        $templateSuffix = '';
        if ($user->isCompanyAdmin($company->id)) {
            $templateSuffix = '-manager';
            $campaigns = Campaign::getCompanyCampaigns($company->id);
        } else {
            $campaigns = $user->getCampaigns($company->id);
        }

        return view('company/dashboard' . $templateSuffix, ['campaigns' => $campaigns, 'company' => $company]);
    }
    
    public function createuser(Company $company)
    {
        return view('company/createuser', ['company' => $company]);
    }

    public function storeuser(Request $request, Company $company)
    {
        $user = User::where('email', $request->get('email'))->first();
        if (empty($user)) {
            $userParameters = $request->only(['name', 'email']);
            $userParameters['password'] = '';
            $user = User::create($userParameters);

            $processRegistration = URL::temporarySignedRoute(
                'registration.complete', Carbon::now()->addMinutes(60), ['id' => $user->getKey()]
            );
            Mail::to($user)->send(new InviteUser($user, $processRegistration));
        }
        $user->companies()->attach($company->id, ['role' => User::ROLE_USER]);
        $this->companyUserActivityLog->attach($user, $company->id, User::ROLE_USER);
        return response()->redirectToRoute('companies.dashboard', ['company' => $company->id]);
    }

    public function users(Company $company)
    {
        return response()->json($company->users);
    }

    public function campaignaccess(Company $company, Campaign $campaign)
    {
        return view('company/campaignaccess', ['campaign' => $campaign, 'company' => $company]);
    }

    public function setcampaignaccess(Request $request, Company $company, Campaign $campaign)
    {
        $allowedUsers = $request->get('allowedusers', []);
        /** @var User $user */
        foreach($company->users as $user) {
            $log = false;
            $hasAccess = $user->hasAccessToCampaign($campaign->id);
            if (in_array($user->id, $allowedUsers) && !$hasAccess) {
                $user->campaigns()->syncWithoutDetaching([$campaign->id]);
                $this->campaignUserActivityLog->attach($user, $campaign->id);
            }
            if (!in_array($user->id, $allowedUsers) && $hasAccess){
                $user->campaigns()->detach([$campaign->id]);
                $this->campaignUserActivityLog->detach($user, $campaign->id);
            }
        }
        return response()->redirectToRoute('companies.campaignaccess', ['company' => $company->id, 'campaign' => $campaign->id]);
    }

    public function preferences(Request $request, Company $company)
    {
        $user = Auth::user();
        $pivot = $user->companies->find($company->id)->pivot;
        return view('company/preferences', ['user' => $user, 'company' => $company, 'pivot' => $pivot]);
    }

    public function setpreferences(Request $request, Company $company)
    {
        /** @var User $user */
        $user = Auth::user();
        $config = $request->get('config', []);
        if (isset($config['timezone'])) {
            $attributes = [
                'config' => [
                    'timezone' => $config['timezone'],
                ],
                'completed_at' => Carbon::now()->toDateTimeString(),
            ];
            $user->companies()->updateExistingPivot($company->id, $attributes);
            $this->companyUserActivityLog->updatePreferences($user, $company->id, $attributes);
        }
        return response()->redirectToRoute('companies.dashboard', ['company' => $company->id]);
    }

    public function useraccess(Company $company, User $user)
    {
        $campaigns = Campaign::getCompanyCampaigns($company->id);
        return view('company/useraccess', ['campaigns' => $campaigns, 'company' => $company, 'user' => $user]);
    }

    public function setuseraccess(Request $request, Company $company, User $user)
    {
        $allowedCampaigns = $request->get('allowedcampaigns', []);

        /** @var Campaign $campaign */
        foreach(Campaign::getCompanyCampaigns($company->id) as $campaign) {
            $hasAccess = $user->hasAccessToCampaign($campaign->id);
            $log = false;
            if (in_array($campaign->id, $allowedCampaigns) && !$hasAccess) {
                $user->campaigns()->syncWithoutDetaching([$campaign->id]);
                $this->campaignUserActivityLog->attach($user, $campaign->id);
            }
            if (!in_array($campaign->id, $allowedCampaigns) && $hasAccess) {
                $user->campaigns()->detach([$campaign->id]);
                $this->campaignUserActivityLog->detach($user, $campaign->id);
            }
        }
        return response()->redirectToRoute('companies.useraccess', ['company' => $company->id, 'user' => $user]);
    }

}

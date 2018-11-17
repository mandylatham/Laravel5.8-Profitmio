<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignUser;
use App\Classes\CampaignUserActivityLog;
use App\Classes\CompanyUserActivityLog;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Mail\InviteUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class CompanyController extends Controller
{

    /**
     * @var Company
     */
    private $company;

    /** @var CompanyUserActivityLog  */
    private $companyUserActivityLog;

    /** @var CampaignUserActivityLog  */
    private $campaignUserActivityLog;

    public function __construct(Company $company, CompanyUserActivityLog $companyUserActivityLog, CampaignUserActivityLog $campaignUserActivityLog)
    {
        $this->company = $company;
        $this->companyUserActivityLog = $companyUserActivityLog;
        $this->campaignUserActivityLog = $campaignUserActivityLog;
    }

    public function campaignIndex(Company $company)
    {
        /** @var User $user */
        $user = auth()->user();
        $templateSuffix = '';
        if ($user->isCompanyAdmin($company->id)) {
            $campaigns = $company->getCampaigns();
            $templateSuffix = '-manager';
        } else {
            $campaigns = $user->getCampaignsForCompany($company);
        }
        return view('company.campaign.index' . $templateSuffix, [
            'campaigns' => $campaigns,
            'company' => $company
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = $this->company->all();
        return view('company.index', ['companies' => $companies]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('company.create');
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
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $company
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
     * @param  \App\Models\Company  $company
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
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        //
    }

    /**
     * Show the template for view the specified resource.
     * If logged user is a company admin, then company.dashboard-manager view is returned
     * else, if logged user is company regular user, then company.dashboard view is returned
     * @param Company $company
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard(Company $company)
    {
        /** @var User $user */
        $user = auth()->user();
        $templateSuffix = '';
        if ($user->isCompanyAdmin($company->id)) {
            $templateSuffix = '-manager';
            $campaigns = $company->getCampaigns();
        } else {
            $campaigns = $user->getCampaignsForCompany($company);
        }

        return view('company.dashboard' . $templateSuffix, ['campaigns' => $campaigns, 'company' => $company]);
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
            $userParameters['first_name'] = '';
            $userParameters['last_name'] = '';
            $userParameters['timezone'] = '';
            $userParameters['username'] = '';
            $user = User::create($userParameters);

            $processRegistration = URL::temporarySignedRoute(
                'registration.complete', Carbon::now()->addMinutes(60), ['id' => $user->getKey()]
            );
            Mail::to($user)->send(new InviteUser($user, $processRegistration));
        }
        $user->companies()->attach($company->id, ['role' => User::ROLE_USER]);
        $this->companyUserActivityLog->attach($user, $company->id, User::ROLE_USER);
        return response()->redirectToRoute('company.user.index', ['company' => $company->id]);
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
        $pivot = $user->companies()->find($company->id)->pivot;
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

    //region User Resource
    /**
     * Show lists of users
     * @param Company $company
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userIndex(Company $company)
    {
        return view('company.user.index', ['company' => $company]);
    }

    /**
     * Show form to create a new user
     * @param Company $company
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userCreate(Company $company)
    {
        return view('company.user.create', ['company' => $company]);
    }

    /**
     * Show form to edit a user
     * @param Company $company
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userEdit(Company $company, User $user)
    {
        return view('company.user.edit', [
            'company' => $company,
            'user' => $user
        ]);
    }

    /**
     * Store a new user in storage
     * @param Company $company
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function userStore(Company $company, Request $request)
    {
        $user = new User([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'is_admin' => false,
            'password' => bcrypt($request->input('password')),
            'phone_number' => $request->input('phone_number'),
            'timezone' => $request->input('timezone')
        ]);
        $user->save();

        $user->companies()->attach($company->id, [
            'role' => $request->input('role')]
        );
        $this->companyUserActivityLog->attach($user, $company->id, $request->input('role'));

        return redirect()->route('company.user.edit', ['company' => $company->id]);
    }
    //endregion
}

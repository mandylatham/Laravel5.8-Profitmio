<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Company;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{
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
        $user = User::where('email', $request->get['email'])->first();
        if (empty($user)) {
            $userParameters = $request->only(['name', 'email']);
            $userParameters['password'] = Hash::make($request->get('password'));
            $user = User::create($userParameters);
        }
        $user->companies()->attach($company->id, ['role' => User::ROLE_USER]);
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
            if (in_array($user->id, $allowedUsers)) {
                $user->campaigns()->syncWithoutDetaching([$campaign->id]);
            } else {
                $user->campaigns()->detach([$campaign->id]);
            }
        }
        return response()->redirectToRoute('companies.campaignaccess', ['company' => $company->id, 'campaign' => $campaign->id]);
    }
}

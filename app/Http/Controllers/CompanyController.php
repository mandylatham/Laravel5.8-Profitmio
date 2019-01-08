<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Classes\CampaignUserActivityLog;
use App\Classes\CompanyUserActivityLog;
use App\Models\Company;
use App\Mail\InviteUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Filesystem\FilesystemManager;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

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

    private $storage;

    private $user;

    private $url;

    public function __construct(Company $company, CompanyUserActivityLog $companyUserActivityLog, CampaignUserActivityLog $campaignUserActivityLog, FilesystemManager $storage, User $user, UrlGenerator $url)
    {
        $this->company = $company;
        $this->companyUserActivityLog = $companyUserActivityLog;
        $this->campaignUserActivityLog = $campaignUserActivityLog;
        $this->storage = $storage;
        $this->user = $user;
        $this->url = $url;
    }

    public function campaignIndex(Company $company, Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(401);
        }
        $campaigns = $company->getCampaigns($request->input('q'))
            ->paginate(15);
        return view('company.campaign.index', [
            'campaigns' => $campaigns,
            'company' => $company
        ]);
    }

    /**
     * Returns all companies for dropdown
     * @param Request $request
     * @return mixed
     */
    public function getForDropdown(Request $request)
    {
        return $this->company
            ->searchByRequest($request)
            ->orderBy('id', 'desc')
            ->paginate($request->input('per_page', 15));
    }

    /**
     * Return all companies for user display
     * @param Request $request
     * @return mixed
     */
    public function getForUserDisplay(Request $request)
    {
        $companies = $this->company->orderBy('id', 'desc');
        if ($request->has('q')) {
            session(['filters.company.get-all.q' => $request->input('q')]);
            $companies->search($request->input('q'));
        }
        return $companies->paginate($request->input('per_page', 15));
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('company.index', [
            'q' => session('filters.company.index.q')
        ]);
    }

    /**
     * Check if this model can be deleted
     * @return bool
     */
    public function canBeDeleted()
    {
        // TODO: Add code to verify if this model can be deleted
        return false;
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

    public function delete(Company $company)
    {
        if ($this->canBeDeleted()) {
            $company->delete();
        }
        return response()->json('Imposible delete this company', 403);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        return view('company.edit', ['company' => $company]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCompanyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCompanyRequest $request)
    {
        $company = new $this->company([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'address2' => $request->input('address2'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'zip' => $request->input('zip'),
            'country' => $request->input('country'),
            'url' => $request->input('url'),
            'facebook' => $request->input('facebook'),
            'twitter' => $request->input('twitter'),
        ]);
        if ($request->hasFile('image')) {
            $company->image_url = $request->file('image')->store('company-image', 's3');
            $this->storage->disk('s3')->setVisibility($company->image_url, 'public');
        }
        $company->save();
        return redirect()->route('company.campaign.index', ['company' => $company->id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Company $company
     * @param StoreCompanyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Company $company, StoreCompanyRequest $request)
    {
        $company->update([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'address2' => $request->input('address2'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'zip' => $request->input('zip'),
            'country' => $request->input('country'),
            'url' => $request->input('url'),
            'facebook' => $request->input('facebook'),
            'twitter' => $request->input('twitter'),
        ]);
        if ($request->hasFile('image')) {
            $company->image_url = $request->file('image')->store('company-image', 's3');
            $this->storage->disk('s3')->setVisibility($company->image_url, 'public');
        }
        $company->save();
        return response()->redirectToRoute('company.campaign.index', ['company' => $company->id]);
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
        $invitation = $user->invitations()->where('company_id', $company->id)->firstOrFail();
        return view('company.user.edit', [
            'company' => $company,
            'user' => $user,
            'userCompanyTimezone' => $invitation->config['timezone'],
            'userCompanyRole' => $invitation->role
        ]);
    }

    /**
     * Invite a new user
     *
     * Admin users can register another Admin Users
     *
     * @param Company $company
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function userStore(Company $company, StoreUserRequest $request)
    {
        $user = $this->user->where('email', $request->input('email'))
            ->first();
        if (($request->input('role') == 'site_admin' && !is_null($user)) || ($user && $user->isAdmin())) {
            return redirect()->back()->withErrors('The email has already been taken.');
        }
        if (!$user) {
            $user = new $this->user();
            $user->is_admin = $request->input('role') == 'site_admin' ? true : false;
            $user->password = '';
            $user->username = $request->input('email');
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->save();
        }
        if ($user->isAdmin()) {
            $processRegistration = URL::temporarySignedRoute(
                'registration.complete.show', Carbon::now()->addMinutes(60), [
                    'id' => $user->getKey()
                ]
            );
        } else {
            // Attach to company if user is not admin
            $user->companies()->attach($company->id, [
                'role' => $request->input('role')
            ]);

            $processRegistration = URL::temporarySignedRoute(
                'registration.complete.show', Carbon::now()->addMinutes(60), [
                    'id' => $user->getKey(),
                    'company' => $company->id
                ]
            );

            $this->companyUserActivityLog->attach($user, $company->id, $request->input('role'));
        }

        Mail::to($user)->send(new InviteUser($user, $processRegistration));

        return redirect()->route('company.user.index', ['company' => $company->id]);
    }

    public function userUpdate(Company $company, User $user, UpdateUserRequest $request)
    {
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->save();

        $invitation = $user->invitations()->where('company_id', $company->id)->firstOrFail();

        $config = $invitation->config;
        $config['timezone'] = $request->input('timezone');

        $invitation->config = $config;
        $invitation->role = $request->input('role');
        $invitation->save();

        return redirect()->route('company.user.index', ['company' => $company->id]);
    }
    //endregion
}

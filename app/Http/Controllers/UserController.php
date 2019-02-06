<?php

namespace App\Http\Controllers;

use App\Classes\CompanyUserActivityLog;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateCompanyDataRequest;
use App\Http\Resources\UserCollection;
use App\Models\Company;
use App\Mail\InviteUser;
use App\Models\User;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
    private $company;

    /** @var CompanyUserActivityLog  */
    private $companyUserActivityLog;

    private $storage;

    private $user;

    public function __construct(Company $company, CompanyUserActivityLog $companyUserActivityLog, FilesystemManager $storage, User $user)
    {
        $this->company = $company;
        $this->companyUserActivityLog = $companyUserActivityLog;
        $this->storage = $storage;
        $this->user = $user;
    }

    public function activate(User $user, Request $request)
    {
        $user->activate($request->input('company'));
        return response()->json(['message' => 'User activated.']);
    }

    /**
     * Return all companies for user display
     * @param Request $request
     * @return mixed
     */
    public function getForUserDisplay(Request $request)
    {
        $userQuery = $this->user->searchByRequest($request);
        $users = $userQuery
            ->select('users.*')
            ->orderBy('users.id', 'desc')
            ->paginate(15);

        return new UserCollection($users);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('user.index', [
            'companySelected' => $this->company->find(session('filters.user.index.company')),
            'q' => session('filters.user.index.q')
        ]);
    }

    public function create()
    {
        return view('user.create', [
            'companies' => $this->company->all()
        ]);
    }

    public function deactivate(User $user, Request $request)
    {
        $user->deactivate($request->input('company'));
        return response()->json(['message' => 'User deactivated.']);
    }

    public function delete(User $user, Request $request)
    {
        $user->delete();
    }

    /**
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->user
            ->where('email', $request->input('email'))
            ->first();

        if (($request->input('role') == 'site_admin' && !is_null($user)) || ($user && $user->isAdmin())) {
            return response()->json(['error' => 'The email has already been taken.'], 400);
        }
        if (!$user) {
            $user = new $this->user();
            $user->is_admin = $request->input('role') == 'site_admin' ? true : false;
            $user->password = '';
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
            if (auth()->user()->isAdmin()) {
                $company = $this->company->findOrFail($request->input('company'));
            } else {
                $company = $this->company->findOrFail(get_active_company());
            }

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

        return response()->json([], 201);
//        return redirect()->route('user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createForm()
    {
        $viewData = [
            'companies' => Company::all()
        ];
        return view('user.new', $viewData);
    }

    /**
     * Return the view to select a company
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function selectActiveCompany(Request $request)
    {
        return view('user.select-company');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $viewData['user'] = $user;
        $viewData['campaigns'] = collect([]);

        if ($user->isAgencyUser()) {
            $viewData['campaigns'] = $user->agencyCampaigns()->get();
        }
        if ($user->access == 'Client') {
            $viewData['campaigns'] = $user->campaigns()->get();
        }

        return view('user.details', $viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('user.edit', [
            'user' => $user,
            'companies' => $user->companies()->orderBy('name', 'asc')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->update($request->except(['password']));

        return response()->json([]);

//        return response()->redirectToRoute('user.edit', ['user' => $user->id]);
    }

    public function updateForm(User $user)
    {
        $viewData['user'] = $user;
        $viewData['companies'] = Company::all();

        return view('user.edit', $viewData);
    }

    public function updateCompanyData(User $user, UpdateCompanyDataRequest $request)
    {
        $invitation = $user->invitations()->where('company_id', $request->input('company'))->firstOrFail();

        if ($request->has('timezone')) {
            $config = $invitation->config;
            $config['timezone'] = $request->input('timezone');
            $invitation->config = $config;
        }
        if ($request->has('role') && $request->input('role') !== 'site_admin') {
            $invitation->role = $request->input('role');
        }
        $invitation->save();

        return response()->json('Resource updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function updateAvatar(User $user, Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $user->addMediaFromRequest('image')
                ->toMediaCollection('profile-photo');
        }
        return response()->json(['location' => $image->getFullUrl()], 201);
    }

    public function view(User $user)
    {
        $loggedUser = auth()->user();
        if ($loggedUser->isAdmin()) {
            $hasCampaigns = $user->getCampaigns()->count() > 0;
        } else {
            $company = Company::findOrFail(get_active_company());
            if ($company->isDealership()) {
                $field = 'dealership_id';
            } else {
                $field = 'agency_id';
            }
            $hasCampaigns = $user->getCampaigns()->where($field, get_active_company())->count() > 0;
        }
        return view('user.detail', [
            'user' => $user,
            'hasCampaigns' => $hasCampaigns,
            'timezones' => $user->getPossibleTimezonesForUser(),
            'campaignCompanySelected' => $this->company->find(session('filters.user.view.campaign-company-selected')),
            'campaignQ' => session('filters.user.view.campaign-q'),
            'companyQ' => session('filters.user.view.company-q'),
        ]);
    }
}

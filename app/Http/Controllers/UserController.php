<?php

namespace App\Http\Controllers;

use App\Classes\CompanyUserActivityLog;
use App\Http\Requests\StoreUserRequest;
use App\Models\Company;
use App\Http\Requests\UserRequest;
use App\Mail\InviteUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
    private $company;

    /** @var CompanyUserActivityLog  */
    private $companyUserActivityLog;

    private $user;

    public function __construct(Company $company, CompanyUserActivityLog $companyUserActivityLog, User $user)
    {
        $this->company = $company;
        $this->companyUserActivityLog = $companyUserActivityLog;
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = auth()->user()->getListOfUsers();
        return view('users.index', ['users' => $users]);
    }

    public function create()
    {
        return view('users.create', []);
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
        if (!$user) {
            $user = new $this->user();
            $user->is_admin = false;
            $user->password = '';
            $user->username = $request->input('email');
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->save();
        }
        $user->companies()->attach(get_active_company(), [
            'role' => $request->input('role')
        ]);
        $company = $this->company->findOrFail(get_active_company());

        $processRegistration = URL::temporarySignedRoute(
            'registration.complete.show', Carbon::now()->addMinutes(60), [
                'id' => $user->getKey(),
                'company' => $company->id
            ]
        );

        Mail::to($user)->send(new InviteUser($user, $processRegistration));

        $this->companyUserActivityLog->attach($user, $company->id, $request->input('role'));

        return redirect()->route('user.index');
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
        return view('users.new', $viewData);
    }

    /**
     * Return the view to select a company
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function selectActiveCompany(Request $request)
    {
        return view('users.select-company');
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

        return view('users.details', $viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $companies = Company::all();
        return view('user/edit', ['user' => $user, 'companies' => $companies]);
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
        if ($request->filled('password')) {
            $user->password = bcrypt($request->get('password'));
        }
        $user->update($request->except(['password']));
//        if ($request->get('is_admin', false)) {
//            $user->is_admin = 1;
//            $user->save();
//        } else {
//            $permissions = [];
//            $oldPermissions = [];
//            foreach ($request->get('role', []) as $companyId => $role) {
//                $permissions[$companyId] = ['role' => $role];
//                $userCompany = $user->companies()->find($companyId);
//                if (!empty($userCompany)) {
//                    $oldPermissions[$companyId] = ['role' => $userCompany->pivot->role];
//                }
//            }
//
//            $changes = $user->companies()->sync($permissions);
//            $this->companyUserActivityLog->sync($user, $changes, $permissions, $oldPermissions);
//        }

        return response()->redirectToRoute('users.index');
    }

    public function updateForm(User $user)
    {
        $viewData['user'] = $user;
        $viewData['companies'] = Company::all();

        return view('users.edit', $viewData);
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
}

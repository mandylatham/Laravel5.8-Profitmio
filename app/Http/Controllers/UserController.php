<?php

namespace App\Http\Controllers;

use App\Classes\CompanyUserActivityLog;
use App\Company;
use App\CompanyUser;
use App\Mail\InviteUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
    /** @var CompanyUserActivityLog  */
    private $companyUserActivityLog;

    public function __construct(CompanyUserActivityLog $companyUserActivityLog)
    {
        $this->companyUserActivityLog = $companyUserActivityLog;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('user/index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companies = Company::all();
        return view('user/create', ['companies' => $companies]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userParameters = $request->only(['name', 'email']);
        $userParameters['password'] = '';
        $user = User::create($userParameters);
        if ($request->get('company') == 'admin') {
            $user->is_admin = 1;
            $user->save();
        } else {
            $user->companies()->attach($request->get('company'), ['role' => $request->get('role')]);
            $this->companyUserActivityLog->attach($user, (int)$request->get('company'), $request->get('role'));
        }

        $processRegistration = URL::temporarySignedRoute(
            'registration.complete', Carbon::now()->addMinutes(60), ['id' => $user->getKey()]
        );
        Mail::to($user)->send(new InviteUser($user, $processRegistration));
        return response()->redirectToRoute('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
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
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $userParameters = $request->only(['name', 'email']);
        if (!empty($request->get('password'))) {
            $userParameters['password'] = Hash::make($request->get('password'));
        }
        $user->update($userParameters);
        if ($request->get('is_admin', false)) {
            $user->is_admin = 1;
            $user->save();
        } else {
            $permissions = [];
            $oldPermissions = [];
            foreach ($request->get('role', []) as $companyId => $role) {
                $permissions[$companyId] = ['role' => $role];
                $userCompany = $user->companies()->find($companyId);
                if (!empty($userCompany)) {
                    $oldPermissions[$companyId] = ['role' => $userCompany->pivot->role];
                }
            }

            $changes = $user->companies()->sync($permissions);
            $this->companyUserActivityLog->sync($user, $changes, $permissions, $oldPermissions);
        }

        return response()->redirectToRoute('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Classes\CompanyUserActivityLog;
use App\Http\Requests\CompleteUserRequest;
use Illuminate\Routing\UrlGenerator;
use App\Models\User;
use App\Models\Company;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompleteController extends Controller
{
    private $carbon;

    /** @var CompanyUserActivityLog */
    private $companyUserActivityLog;

    private $company;

    private $user;

    private $url;

    public function __construct(Carbon $carbon, Company $company, CompanyUserActivityLog $companyUserActivityLog, User $user, UrlGenerator $url)
    {
        $this->carbon = $carbon;
        $this->company = $company;
        $this->companyUserActivityLog = $companyUserActivityLog;
        $this->user = $user;
        $this->url = $url;
    }

    public function show(Request $request)
    {
        $user = $this->user->find($request->get('id'));
        if (auth()->check()) {
            session()->forget('activeCompany');
            auth()->user()->leaveImpersonation();
            auth()->logout();
        }
        $company = null;
        if ($user->isAdmin() && $user->isProfileCompleted()) {
            abort(403);
        }

        $company = $user->companies()->where('companies.id', $request->get('company'))->first();
        if ($user->isCompanyProfileReady($company)) {
            abort(403);
        }

        $sufix = $user->isProfileCompleted() ? '-full' : '';
        return view('auth.complete' . $sufix, [
            'user' => $user,
            'completeRegistrationSignedUrl' => $this->url->temporarySignedRoute('registration.complete.store', $this->carbon::now()->addMinutes(240), [
                'user' => $user->id,
                'company' => $company ? $company->id : null
            ]),
            'company' => $company,
            'timezone' => $user->isAdmin() ? '' :$user->getTimezone($company)
        ]);
    }

    public function set(CompleteUserRequest $request)
    {
        /** @var User $user */
        $user = $this->user->find($request->input('user'));
        if ($user->isAdmin() || !$user->isProfileCompleted()) {
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->phone_number = $request->input('phone_number');
            $user->password = bcrypt($request->input('password'));
            $user->save();
        }

        $data = [
            'config' => [
                'timezone' => $request->input('timezone')
            ],
            'completed_at' => $this->carbon->now()->toDateTimeString()
        ];

        \Log::debug('complete registration for company id ' . $request->input('company'));
        $user->companies()->updateExistingPivot($request->input('company'), $data);
        $this->companyUserActivityLog->updatePreferences($user, (int)$request->input('company'), $data);

        return response()->json([
            'redirect_url' => route('login')
        ]);
    }
}

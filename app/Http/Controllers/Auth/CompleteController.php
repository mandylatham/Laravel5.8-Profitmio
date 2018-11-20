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
        if (auth()->check()) {
            auth()->logout();
        }
        $user = $this->user->find($request->get('id'));
        $company = $user->companies()->where('companies.id', $request->get('company'))->first();
        $sufix = $user->isProfileCompleted() ? '-full' : '';
        return view('auth.complete' . $sufix, [
            'user' => $user,
            'completeRegistrationSignedUrl' => $this->url->temporarySignedRoute('registration.complete.store', $this->carbon::now()->addMinutes(5), [
                'user' => $user->id,
                'company' => $company->id
            ]),
            'company' => $company
        ]);
    }

    public function set(CompleteUserRequest $request)
    {
        /** @var User $user */
        $user = $this->user->find($request->input('user'));

        if (!$user->isProfileCompleted()) {
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->username = $request->input('username');
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

        $user->companies()->updateExistingPivot($request->input('company'), $data);
        $this->companyUserActivityLog->updatePreferences($user, $request->get('company'), $data);

        return response()->redirectToRoute('login');
    }
}

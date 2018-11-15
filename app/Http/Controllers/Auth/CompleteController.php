<?php

namespace App\Http\Controllers\Auth;

use App\Classes\CompanyUserActivityLog;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class CompleteController extends Controller
{
    /** @var CompanyUserActivityLog  */
    private $companyUserActivityLog;

    public function __construct(CompanyUserActivityLog $companyUserActivityLog)
    {
        $this->companyUserActivityLog = $companyUserActivityLog;
    }

    public function show(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
        }
        $user = User::find($request->get('id'));
        return view('auth/complete', ['user' => $user]);
    }

    public function set(Request $request)
    {
        /** @var User $user */
        $user = User::find($request->get('id'));

        Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ])->validate();

        $user->name = $request->get('name');
        $user->password = $userParameters['password'] = Hash::make($request->get('password'));
        $user->save();

        $config = $request->get('config', []);
        if (isset($config['timezone'])) {
            foreach ($config['timezone'] as $companyId => $timezone) {
                $attributes = [
                    'config' => [
                        'timezone' => $timezone
                    ],
                    'completed_at' => Carbon::now()->toDateTimeString(),
                ];
                $user->companies()->updateExistingPivot($companyId, $attributes);
                $this->companyUserActivityLog->updatePreferences($user, $companyId, $attributes);
            }
        }
        return response()->redirectToRoute('login');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, User $user)
    {
        $companies = $user->companies;
        if (count($companies) == 1) {
            return response()->redirectToRoute('companies.dashboard', ['company' => $companies[0]->id]);
        }

        return redirect($this->redirectTo);
    }

    public function login(LoginRequest $request)
    {
        if (auth()->attempt($request->only('email', 'password'))) {
            if (auth()->user()->isAdmin()) {
                return response()->json(['redirect_url' => route('campaigns.index')]);
//                return redirect()->route('campaign.index');
            } else if (auth()->user()->hasActiveCompanies()) {
                return response()->json(['redirect_url' => route('dashboard')]);
//                return redirect()->route('dashboard');
            } else {
                auth()->logout();
                return response()->json(['message' => 'Your account does not have any available company.'], 403);
            }
        }

        return response()->json([
            'message' => 'These credentials do not match our records.',
        ], 403);
    }

    public function showForgetPasswordForm(Request $request)
    {
        return view('auth.passwords.forget');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect()->route('login');
    }
}

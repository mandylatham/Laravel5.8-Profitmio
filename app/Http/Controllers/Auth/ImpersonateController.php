<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    public function login(User $user)
    {
        Auth::user()->impersonate($user);
        return redirect($this->redirectTo);
    }

    public function leave()
    {
        Auth::user()->leaveImpersonation();
        return redirect($this->redirectTo);
    }

}

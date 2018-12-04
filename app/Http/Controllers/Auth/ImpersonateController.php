<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;

class ImpersonateController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    public function login(User $user)
    {
        auth()->user()->impersonate($user);
        return redirect($this->redirectTo);
    }

    public function leave()
    {
        auth()->user()->leaveImpersonation();
        return redirect($this->redirectTo);
    }

}

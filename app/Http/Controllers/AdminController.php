<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminController extends Controller
{
    public function impersonateUser(User $user)
    {
        auth()->user()->impersonate($user);
        return redirect()->route('dashboard');
    }

    public function impersonateLeave()
    {
        // Reject if user is not admin, we can't user middleware or policy because auth()->user() returns impersonated user, not admin user
        if (auth()->user()->isAdmin()) {
            abort(403);
        }
        auth()->user()->leaveImpersonation();
        return redirect()->route('campaign.index');
    }
}

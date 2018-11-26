<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\InviteUser;
use Carbon\Carbon;
use Illuminate\Routing\UrlGenerator;
use App\Http\Requests\ResendInvitationRequest;
use Illuminate\Mail\Mailer;
use App\Models\Company;

class AdminController extends Controller
{
    protected $company;

    protected $mail;

    protected $url;

    protected $user;

    public function __construct(Company $company, UrlGenerator $url, Mailer $mail, User $user)
    {
        $this->company = $company;

        $this->mail = $mail;
        $this->url = $url;
        $this->user = $user;
    }

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
        session()->forget('activeCompany');
        auth()->user()->leaveImpersonation();
        return redirect()->route('campaign.index');
    }

    public function resendInvitation(ResendInvitationRequest $request)
    {
        $user = $this->user->findOrfail($request->input('user'));
        $company = $this->company->findOrFail($request->input('company'));
        if ($user->isCompanyProfileReady($company)) {
            abort(403);
        }

        $processRegistration = $this->url->temporarySignedRoute(
            'registration.complete.show', Carbon::now()->addMinutes(60), [
                'id' => $user->id,
                'company' => $request->input('company')
            ]
        );

        $this->mail->to($user)->send(new InviteUser($user, $processRegistration));

        return redirect()->back();
    }
}

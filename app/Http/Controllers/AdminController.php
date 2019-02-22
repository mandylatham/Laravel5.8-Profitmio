<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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

    public function impersonateUser(Request $request, User $user)
    {
        session()->forget('activeCompany');
        if ($request->has('company')) {
            session(['activeCompany' => $request->input('company')]);
        }
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
        return redirect()->route('campaigns.index');
    }

    public function resendInvitation(ResendInvitationRequest $request)
    {
        $user = $this->user->findOrfail($request->input('user'));
        $company = $this->company->findOrFail($request->input('company'));
        if ($user->isCompanyProfileReady($company)) {
            abort(403);
        }

        $processRegistration = $this->url->temporarySignedRoute(
            'registration.complete.show', Carbon::now()->addMinutes(1440), [
                'id' => $user->id,
                'company' => $request->input('company')
            ]
        );

		\Log::debug("Resending Invite for user (id:{$user->id}) with url: {$processRegistration}");

        $this->mail->to($user)->send(new InviteUser($user, $company, $processRegistration));

        return response()->json(['message' => 'Invitation sent.']);
//
//        return redirect()->back();
    }
}

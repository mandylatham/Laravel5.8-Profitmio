<?php

namespace App\Providers;

use App\Models\Campaign;
use App\Models\Company;
use App\Models\Recipient;
use App\Models\Impersonation\ImpersonatedUser;
use App\Models\Response;
use App\Policies\CampaignPolicy;
use App\Policies\CompanyPolicy;
use App\Models\CampaignScheduleTemplate;
use App\Policies\CampaignScheduleTemplatePolicy;
use App\Policies\RecipientPolicy;
use App\Policies\ResponsePolicy;
use App\Policies\UserPolicy;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Company::class => CompanyPolicy::class,
        Campaign::class => CampaignPolicy::class,
        Recipient::class => RecipientPolicy::class,
        CampaignScheduleTemplate::class => CampaignScheduleTemplatePolicy::class,
        User::class => UserPolicy::class,
        Response::class => ResponsePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->defineGates();

        $this->defineImpersonateAuthGate();
    }

    private function defineGates(): void
    {
        Gate::before(function (User $user, $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });

        Gate::define('only-admin', function ($user, $post) {
            return $user->id == $post->user_id;
        });
        Gate::define('company.viewforpreferences', 'App\Policies\CompanyPolicy@viewForPreferences');
        Gate::define('company.view', 'App\Policies\CompanyPolicy@view');
        Gate::define('company.create', 'App\Policies\CompanyPolicy@create');
        Gate::define('company.update', 'App\Policies\CompanyPolicy@update');
        Gate::define('company.delete', 'App\Policies\CompanyPolicy@delete');

        Gate::define('company.manage', 'App\Policies\CompanyPolicy@manage');


        Gate::define('campaign.create', 'App\Policies\CampaignPolicy@create');
        Gate::define('campaign.manage', 'App\Policies\CampaignPolicy@manage');
    }

    private function defineImpersonateAuthGate(): void
    {
        Auth::viaRequest('impersonuser', function () {
            /** @var User $user */
            if (! $user = Auth::user()) {
                return;
            }

            if ($user->isImpersonated()) {
                $manager = app('impersonate');
                return ImpersonatedUser::findOrCreateImpersonatedUser($user->id, $manager->getImpersonatorId());
            } else {
                return $user;
            }
        });
    }


}

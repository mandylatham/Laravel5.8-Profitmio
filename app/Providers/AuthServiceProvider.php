<?php

namespace App\Providers;

use App\Models\Campaign;
use App\Models\Company;
use App\Policies\CampaignPolicy;
use App\Policies\CompanyPolicy;
use App\Models\User;
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
        'App\Model' => 'App\Policies\ModelPolicy',
        Company::class => CompanyPolicy::class,
        Campaign::class => CampaignPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function (User $user, $ability) {
            if ($user->isAdmin()) {
                return true;
            }
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


}

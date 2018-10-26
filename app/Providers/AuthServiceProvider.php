<?php

namespace App\Providers;

use App\Company;
use App\Policies\CompanyPolicy;
use App\User;
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

        Gate::define('company.view', 'App\Policies\CompanyPolicy@view');
        Gate::define('company.create', 'App\Policies\CompanyPolicy@create');
        Gate::define('company.update', 'App\Policies\CompanyPolicy@update');
        Gate::define('company.delete', 'App\Policies\CompanyPolicy@delete');
    }


}

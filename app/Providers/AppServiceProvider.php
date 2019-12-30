<?php

namespace App\Providers;

use App\Repositories\LeadSearch;
use Illuminate\Support\Facades\View;
use Laravel\Dusk\DuskServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $client = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        $adapter = new \Http\Adapter\Guzzle6\Client($client);

        $this->app->bind('s3.client', $adapter);
        $this->app->bind('mailgun.client', $adapter);

        if ($this->app->environment('local', 'testing')) {
            #$this->app->register(DuskServiceProvider::class);
        }

        /*
         * LeadSearchRepository
         */
        $this->app->singleton(LeadSearch::class, function () {
            return new LeadSearch();
        });

        View::composer('*', function ($view) {
            $view->with('loggedUser', auth()->user());
        });
    }
}

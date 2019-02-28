<?php

namespace App\Providers;

use Laravel\Dusk\Browser;
use Illuminate\Support\ServiceProvider;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Browser::macro('vueSelect', function ($element, $itemIndexToSelect) {
            $this->click($element)
                ->pause(1000)
                ->click('.dropdown-menu li:nth-child(' . ($itemIndexToSelect+1) . ')')
                ->pause(1000);

            return $this;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

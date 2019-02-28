<?php

namespace App\Providers;

use Laravel\Dusk\Browser;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Macro to select an item in vue-select
        Browser::macro('vueSelect', function ($element, $itemIndexToSelect) {
            $this->click($element)
                ->pause(1000)
                ->click('.dropdown-menu li:nth-child(' . ($itemIndexToSelect+1) . ')')
                ->pause(1000);

            return $this;
        });

        // Macro to select a date using date-pick input component
        Browser::macro('datePickSelect', function ($element, $date) {
            $date = new Carbon($date);
            $diffInMonths = $date->diffInMonths(Carbon::now(), true);
            $this->click($element)
                ->waitFor('.vdpOuterWrap');
            if ($diffInMonths > 0) {
                while($diffInMonths > 0) {
                    $this->click('.vdpArrow.vdpArrowNext');
                    $diffInMonths--;
                }
            }
            if ($diffInMonths < 0) {
                while($diffInMonths < 0) {
                    $this->click('.vdpArrow.vdpArrowPrev');
                    $diffInMonths++;
                }
            }
            $this->click('.vdpOuterWrap td[data-id="' . $date->format('Y-n-j') . '"]')
                ->waitUntilMissing('.vdpOuterWrap');

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

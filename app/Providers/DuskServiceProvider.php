<?php

namespace App\Providers;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Console\InstallCommand;
use Laravel\Dusk\Console\DuskCommand;
use Laravel\Dusk\Console\DuskFailsCommand;
use Laravel\Dusk\Console\MakeCommand;
use Laravel\Dusk\Console\PageCommand;
use Laravel\Dusk\Console\ComponentCommand;
use Exception;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Assert as PHPUnit;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * @throws Exception
     */
    public function register()
    {

        if ($this->app->environment('production')) {
            throw new Exception('It is unsafe to run Dusk in production.');
        }

        if (!file_exists(base_path('.env.dusk'))) {
            throw new Exception('.env.dusk file does\'t exists, it\'s unsafe to run Dusk, please copy .env.dusk.example to .env.dusk to solve this issue.');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                DuskCommand::class,
                DuskFailsCommand::class,
                MakeCommand::class,
                PageCommand::class,
                ComponentCommand::class,
            ]);
        }

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

        // Macro to select a date and time using date-picker plugin
        Browser::macro('selectDateTime', function ($element, $date, $outsideElement) {
            $date = new Carbon($date);
            $diffInMonths = Carbon::createFromFormat('mY', $date->format('mY'))->diffInMonths(Carbon::createFromFormat('mY', Carbon::now()->format('mY')), true);
            $this->click($element)
                ->waitFor('.mx-calendar.mx-calendar-panel-date');
            if ($diffInMonths > 0) {
                while($diffInMonths > 0) {
                    $this->click('.mx-calendar.mx-calendar-panel-date .mx-icon-next-month');
                    $diffInMonths--;
                }
            }
            if ($diffInMonths < 0) {
                while($diffInMonths < 0) {
                    $this->click('.mx-calendar.mx-calendar-panel-date .mx-icon-last-month');
                    $diffInMonths++;
                }
            }
            $this->click('.mx-calendar.mx-calendar-panel-date .mx-panel-date td[title="' . $date->format('m/d/Y') . '"]')
                ->click($outsideElement)
                ->waitUntilMissing('.mx-calendar.mx-calendar-panel-date');

            return $this;
        });

        Browser::macro('assertElementsCounts', function ($selector, $count) {
            PHPUnit::assertEquals($count, count($this->elements($selector)));
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

        Route::get('/_dusk/login/{userId}/{guard?}', [
            'middleware' => 'web',
            'uses' => 'Laravel\Dusk\Http\Controllers\UserController@login',
        ]);

        Route::get('/_dusk/logout/{guard?}', [
            'middleware' => 'web',
            'uses' => 'Laravel\Dusk\Http\Controllers\UserController@logout',
        ]);

        Route::get('/_dusk/user/{guard?}', [
            'middleware' => 'web',
            'uses' => 'Laravel\Dusk\Http\Controllers\UserController@user',
        ]);
    }
}

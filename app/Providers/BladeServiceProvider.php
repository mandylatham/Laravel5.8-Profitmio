<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return string
     */
    public function boot()
    {
        Blade::directive('role', function ($expression) {
            return "<?php
                if ($expression == 'admin') {
                    echo '<span class=\"badge badge-success\" style=\"font-size: 85%;\">Company Admin</span>';
                } else if ($expression == 'user') {
                    echo '<span class=\"badge badge-dark\" style=\"font-size: 85%;\">Company User</span>';
                } else if ($expression == 'site_admin') {
                    echo '<span class=\"badge badge-primary\" style=\"font-size: 85%;\">Site Admin</span>';
                }
            ?>";
        });
        Blade::directive('status', function ($expression) {
            return "<?php
                if ($expression) {
                    echo '<span class=\"badge badge-success\" style=\"font-size: 85%;\">ACTIVE</span>';
                } else {
                    echo '<span class=\"badge badge-danger\" style=\"font-size: 85%;\">INACTIVE</span>';
                }
            ?>";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

}

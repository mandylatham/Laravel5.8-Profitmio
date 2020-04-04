<?php
namespace App\Providers;

use App\Services\TextToValueResponder;
use Illuminate\Support\ServiceProvider;

class TextToValueResponderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('TextToValueResponder', TextToValueResponder::class);
    }
}

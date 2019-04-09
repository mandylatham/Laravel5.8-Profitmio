<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SentimentServiceProvider extends ServiceProvider
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
        $this->app->singleton('Aws\Comprehend\ComprehendClient', function ($app) {
            return $app->make('aws')->createClient('comprehend');
        });

        $this->app->singleton('AwsComprehandService', 'App\Services\AwsComprehendService');
        $this->app->singleton('SentimentService', 'App\Services\SentimentService');
    }
}

<?php
namespace App\Providers;

use App\Models\Response;
use App\Observers\ImpersonateObserver;

class ImpersonateServiceProvider extends \Lab404\Impersonate\ImpersonateServiceProvider
{
    public function boot()
    {
        parent::boot();
        /*
        * Register model observers
        * We will log actions for this entities
        */
        Response::observe(ImpersonateObserver::class);
    }
}

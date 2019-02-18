<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\CampaignDropQueued' => [
            'App\Listeners\SendClientDropEmail',
        ],
        'App\Events\ServiceDeptLabelAdded' => [
            'App\Listeners\SendNotificationForServiceDepartment',
        ],
        'App\Events\AppointmentCreated' => [
            'App\Listeners\SendAppointmentNotifications',
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

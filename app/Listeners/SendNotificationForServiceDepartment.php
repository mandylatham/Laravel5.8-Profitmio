<?php

namespace App\Listeners;

use App\Events\ServiceDeptLabelAdded;
use App\Mail\ServiceDeptNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendNotificationForServiceDepartment
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ServiceDeptLabelAdded  $event
     * @return void
     */
    public function handle(ServiceDeptLabelAdded $event)
    {
        $recipient = $event->getRecipient();
        $recipient->campaign->service_dept;
        if ($recipient->campaign->service_dept_email) {
            $serviceDeptEmails = explode(',', $recipient->campaign->service_dept_email);
            foreach ($serviceDeptEmails as $email) {
                $email = trim($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($email)->send(new ServiceDeptNotification($recipient));
                }
            }
        }
    }
}

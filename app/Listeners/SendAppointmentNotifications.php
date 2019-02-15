<?php

namespace App\Listeners;

use App\Events\AppointmentCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAppointmentNotifications
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
     * @param  AppointmentCreated  $event
     * @return void
     */
    public function handle(AppointmentCreated $event)
    {
        $appointment = $event->appointment;
        $campaign    = $appointment->campaign;
        
        if (in_array($appointment->type, [Appointment::TYPE_APPOINTMENT])) {
            if ($campaign->adf_crm_export) {
                $alert_emails = (array)$campaign->adf_crm_export_email;
                foreach ($alert_emails as $email) {
                    $email = trim($email);
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $this->log->error("AppointmentController@insert (line 82): Skipping crm notification to invalid email, $email");
                        $this->log->channel('operations')->notice('SendAppointmentNotifications: unable to send recipient (id:'.$appointment->recipient->id.') to CRM using invalid email (email:'.$email.')');
                        continue;
                    }
                    try {
                        $this->mail->to($email)->send(new CrmNotification($campaign, $appointment));
                        $appointment->recipient->update(['sent_to_crm' => true])
                            && \Log::channel('operations')->info('recipient (id:'.$appointment->recipient->id.') sent to crm');
                        $this->log->debug("AppointmentController@insert: Sent crm alerts for appointment #{$appointment->id}");
                        $this->log->channel('operations')->info('SendAppointmentNotifications: recipient  (id:'.$appointment->recipient->id.') pushed to CRM using email (email:'.$email.')');
                    } catch (\Exception $e) {
                        $this->log->error("Unable to send crm notification: " . $e->getMessage());
                    }
                }
            }
        }

        if ($campaign->lead_alerts) {
            $alert_emails = (array)$campaign->lead_alert_email;

            foreach ($alert_emails as $email) {
                $email = trim($email);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->log->error("AppointmentController@insert (line 82): Skipping lead notification to invalid email, $email");
                    $this->log->channel('operations')->notice('SendAppointmentNotifications: unable to send lead notification for recipient (id:'.$appointment->recipient->id.') to invalid email (email:'.$email.')');
                    continue;
                }

                try {
                    $this->mail->to($email)->send(new LeadNotification($campaign, $appointment));
                    $this->log->debug("AppointmentController@insert: Sent lead alerts for appointment #{$appointment->id}");
                    $this->log->channel('operations')->info('SendAppointmentNotifications: appointment (id:'.$appointment->id.') sent to lead notification email (email:'.$email.')');
                } catch (\Exception $e) {
                    $this->log->error("Unable to send lead notification: " . $e->getMessage());
                    $this->log->channel('operations')->error('SendAppointmentNotifications: appointment (id:'.$appointment->id.') UNABLE TO SEND LEAD NOTIFICATION TO EMAIL (email:'.$email.'): ' . $e->getMessage());
                }
            }
        }

        if (($appointment->type == Appointment::TYPE_CALLBACK) && ($campaign->sms_on_callback == 1)) {
            try {
                $phone = $campaign->phones()->whereCallSourceName('sms')->orderBy('id', 'desc')->firstOrFail();
                $from = $phone->phone_number;
                $to = $campaign->sms_on_callback_number;
                $message = $this->getCallbackMessage($appointment);
                TwilioClient::sendSms($from, $to, $message);
                $this->log->channel('operations')->info('SendAppointmentNotifications: callback (id:'.$appointment->id.') sent to callback sms notification number (phone:'.$to.')');
            } catch (\Exception $e) {
                Log::error("Unable to send callback SMS: " . $e->getMessage());
            }
        }
    }
}

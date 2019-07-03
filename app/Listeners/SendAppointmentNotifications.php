<?php

namespace App\Listeners;

use App\Events\AppointmentCreated;
use App\Models\Appointment;
use App\Services\CrmService;
use App\Mail\LeadNotification;
use App\Facades\TwilioClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Log\Logger;
use Illuminate\Mail\Mailer;

class SendAppointmentNotifications
{
    private const CALLBACK_MESSAGE = 'Profit Miner callback requested for %s at %s';

    /**
     * Crm Service
     */
    private $crm;

    /**
     * Log Service
     */
    private $log;

    /**
     * Mail Service
     */
    private $mail;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(CrmService $crm, Logger $log, Mailer $mail)
    {
        $this->crm = $crm;
        $this->log = $log;
        $this->mail = $mail;
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
            $this->crm->sendAppointment($appointment);
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
                $to_numbers = $campaign->sms_on_callback_number;
				if (count($to_numbers)) {
					foreach ($to_numbers as $to) {
					$message = $this->getCallbackMessage($appointment);
					TwilioClient::sendSms($from, $to, $message);
					$this->log->channel('operations')->info('SendAppointmentNotifications: callback (id:'.$appointment->id.') sent to callback sms notification number (phone:'.$to.')');
					}
				} else {
					$this->log->error("Campaign {$campaign->id} is misconfigured, causing callback notifications to fail");
				}
            } catch (\Exception $e) {
                $this->log->error("Unable to send callback SMS: " . $e->getMessage());
            }
        }
    }

    /**
     * @param Appointment $appointment
     * @return string
     */
    private function getCallbackMessage(Appointment $appointment): string
    {
        $name = $appointment->first_name . ' ' . $appointment->last_name;

        return sprintf(self::CALLBACK_MESSAGE, $name, $appointment->phone_number);
    }
}

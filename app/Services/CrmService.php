<?php

namespace App\Services;

use App\Events\CampaignCountsUpdated;
use App\Classes\MailgunService;
use App\Mail\CrmAppointmentNotification;
use App\Mail\CrmManualRecipientNotification;
use App\Models\Appointment;
use App\Models\Campaign;
use App\Models\Company;
use App\Models\Recipient;
use App\Models\User;
use App\Services\PusherBroadcastingService;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CrmService
{
    const APPOINTMENT = 'appointment';
    const RECIPIENT = 'recipient';

    /**
     * CrmService constructor.
     * @param Appointment $appointment
     * @param Carbon      $carbon
     * @param Campaign    $campaign
     * @param Recipient   $recipient
     * @param Logger      $log
     * @param Mailer      $mail
     */
    public function __construct(
        Appointment $appointment,
        Carbon $carbon,
        Campaign $campaign,
        Company $company,
        Recipient $recipient,
        Logger $log,
        Mailer $mail
    ) {
        $this->appointment = $appointment;
        $this->carbon = $carbon;
        $this->campaign = $campaign;
        $this->company = $company;
        $this->log = $log;
        $this->recipient = $recipient;
        $this->mail = $mail;
    }
    public function sendAppointment(Appointment $appointment)
    {
        $send = function ($email) use ($appointment) {
            try {
                $this->mail->to($email)->send(new CrmAppointmentNotification($appointment));
                $appointment->recipient->update(['sent_to_crm' => true])
                    && \Log::channel('operations')->info('recipient (id:'.$appointment->recipient->id.') sent to crm');
                $this->log->debug("CrmService: Sent crm alerts for appointment #{$appointment->id}");
            } catch (\Exception $e) {
                $this->log->error("CrmService: Unable to send crm notification: " . $e->getMessage());
            }
        };

        $this->sendToCrm($appointment->campaign, $send);
    }

    public function sendRecipient(Recipient $recipient, User $user)
    {
        if ($recipient->sent_to_crm) {
            $this->log->warning("CrmService: unwilling to send recipient (id:{$recipient->id}) to crm more than once");
            return;
        }

        $send = function ($email) use ($recipient, $user) {
            try {
                $this->mail->to($email)->send(new CrmManualRecipientNotification($recipient, $user));
                $recipient->update(['sent_to_crm' => true])
                    && \Log::channel('operations')->info('recipient (id:'.$recipient->id.') sent to crm');
                $this->log->debug("CrmService: Recipient (id:{$recipient->id}) manually sent to CRM");
            } catch (\Exception $e) {
                $this->log->error("CrmService: Unable to send crm notification: " . $e->getMessage());
            }
        };

        $this->sendToCrm($recipient->campaign, $send);
    }


    protected function isCrmEnabledOnCampaign(Campaign $campaign)
    {
        return $campaign->adf_crm_export;
    }

    protected function validEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    protected function sendToCrm(Campaign $campaign, Closure $send)
    {
        if (! $this->isCrmEnabledOnCampaign($campaign)) {
            $this->log->warning("crm push requested on campaign (id:".$campaign->id.") without crm integration enabled");
            return;
        }

        foreach ((array)$campaign->adf_crm_export_email as $email) {
            $email = trim($email);
            if (! $this->validEmail($email)) {
                $this->log->channel('operations')->warning('CrmService: unable to use CRM with invalid email (email:'.$email.')');
                continue;
            }

            $send($email);
        }
    }
}

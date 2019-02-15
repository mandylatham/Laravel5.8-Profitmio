<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CrmAppointmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $campaign;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $this->campaign = $appointment->campaign;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(['email' => 'no-reply@mg.automotive-alerts.com', 'name' => 'Profit Miner'])
            ->subject('Profit Miner Appointment Integration')
            ->text('emails.crm-appointment-notification')
            ->with([
                'campaign' => $this->campaign,
                'appointment' => $this->appointment,
            ]);
    }
}

<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LeadNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Campaign $campaign, Appointment $appointment)
    {
        $this->campaign = $campaign;

        $this->appointment = $appointment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(['email' => 'no-reply@mg.automotive-alerts.com', 'name' => 'Profit Miner'])
            ->subject('Profit Miner Lead Notification')
            ->view('emails.lead-notification')
            ->with([
                'campaign' => $this->campaign,
                'appointment' => $this->appointment,
            ]);
    }
}

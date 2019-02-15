<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\Recipient;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CrmManualRecipientNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $campaign;
    public $recipient;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Recipient $recipient, User $user)
    {
        $this->recipient = $recipient;
        $this->campaign = $recipient->campaign;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(['email' => 'no-reply@mg.automotive-alerts.com', 'name' => 'Profit Miner'])
            ->subject('Profit Miner Lead Integration')
            ->text('emails.crm-lead-notification')
            ->with([
                'campaign' => $this->campaign,
                'recipient' => $this->recipient,
                'user' => $this->user,
            ]);
    }
}

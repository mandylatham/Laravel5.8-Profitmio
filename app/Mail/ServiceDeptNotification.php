<?php

namespace App\Mail;

use App\Models\Recipient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ServiceDeptNotification extends Mailable
{
    use Queueable, SerializesModels;

    /** @var  Recipient */
    private $recipient;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Recipient $recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(['email' => 'no-reply@mg.automotive-alerts.com', 'name' => 'Profit Miner'])
                    ->subject($this->recipient->campaign->name. ' Service Department Notification')
                    ->text('emails.service_dept-notification')
                    ->with([
                               'recipient' => $this->recipient,
                           ]);
    }
}

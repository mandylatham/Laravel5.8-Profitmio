<?php

namespace App\Mail;

use DateTime;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RenewFacebookTokenNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(DateTime $expiresAt)
    {
        $this->expiresAt = new Carbon($expiresAt);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(['email' => 'no-reply@mg.automotive-alerts.com', 'name' => 'Profit Miner'])
            ->subject('Profit Miner Admin Notification')
            ->view('emails.refresh-facebook-token')
            ->with([
                'daysToExpire' => $this->expiresAt->diffForHumans(['syntax' => CarbonInterface::DIFF_ABSOLUTE]),
            ]);
    }
}

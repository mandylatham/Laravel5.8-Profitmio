<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Drop;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InviteUser extends Mailable
{
    use Queueable, SerializesModels;

    /** @var  User */
    public $user;

    /** @var  string */
    public $processLink;

    /** @var  string */
    public $company;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Company $company, string $processLink)
    {
        $this->user = $user;
        $this->processLink = $processLink;
        $this->company = $company;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(['email' => 'no-reply@alerts.profitminer.io', 'name' => 'Profit Miner'])
            ->subject('Profit Miner Invitation')
            ->view('emails.inviteuser')
            ->with([
                'user' => $this->user,
                'processLink' => $this->processLink,
                'company' => $this->company,
            ]);
    }
}

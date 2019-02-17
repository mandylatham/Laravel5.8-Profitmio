<?php

namespace App\Mail;

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

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $processLink)
    {
        $this->user = $user;
        $this->processLink = $processLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.inviteuser');
    }
}

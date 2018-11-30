<?php

namespace App\Events;

use App\Models\Recipient;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;

class ServiceDeptLabelAdded
{
    use SerializesModels;

    /** @var  Recipient */
    private $recipient;

    /**
     * Create a new event instance.
     *
     * @param $recipient Recipient
     */
    public function __construct(Recipient $recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    public function getRecipient(): Recipient
    {
        return $this->recipient;
    }
}

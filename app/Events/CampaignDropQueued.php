<?php

namespace App\Events;

use App\Models\Drop;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;

class CampaignDropQueued implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $drop;
    public $campaign;
    public $client;

    /**
     * Create a new event instance.
     * CampaignDropQueued constructor.
     * @param Drop $drop
     */
    public function __construct(Drop $drop)
    {
        $this->drop = $drop;
        $this->campaign = $drop->campaign;
        $this->client = $drop->campaign->client;
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
}

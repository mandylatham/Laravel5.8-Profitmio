<?php

namespace App\Events;

use App\Models\Appointment;
use App\Models\Campaign;
use App\Models\Recipient;
use App\Models\Response;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RecipientTextResponseReceived implements ShouldBroadcast
{
    use SerializesModels;

    public $broadcastQueue = 'pusher-queue';

    private $campaign;

    private $recipient;

    private $response;

    /**
     * Create a new event instance.
     *
     * @param Campaign  $campaign  Campaign
     * @param Recipient $recipient Recipient
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('campaign.' . $this->response->campaign_id);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'recipient.' . $this->response->recipient_id . '.text-response-received';
    }

    public function broadcastWith()
    {
        return [
            'response' => $this->response
        ];
    }
}

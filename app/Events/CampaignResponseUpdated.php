<?php

namespace App\Events;

use App\Appointment;
use App\Campaign;
use App\Recipient;
use App\Response;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CampaignResponseUpdated implements ShouldBroadcast
{
    use SerializesModels;

    /** @var  Campaign */
    private $campaign;
    /** @var  Recipient */
    private $recipient;

    /**
     * Create a new event instance.
     *
     * @param $campaign Campaign
     * @param $recipient Recipient
     */
    public function __construct(Campaign $campaign, Recipient $recipient)
    {
        $this->recipient = $recipient;
        $this->campaign = $campaign;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('campaign.' . $this->campaign->id);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'response.'.$this->recipient->target_id.'.updated';
    }

    public function broadcastWith()
    {
        return [
            'appointments' => $this->getAppointments(),
            'emailthreads' => $this->getEmailThreads(),
            'textthreads' => $this->getTextThreads(),
            'phonethreads' => $this->getPhoneThreads(),
            'recipient' => $this->recipient->toArray(),
        ];
    }

    private function getAppointments()
    {
        return Appointment::where('target_id', $this->recipient->target_id)->get()->toArray();
    }

    private function getEmailThreads()
    {
        return Response::where('campaign_id', $this->campaign->id)
                ->where('target_id', $this->recipient->id)
                ->where('type', 'email')
                ->get()
                ->toArray();
    }

    private function getTextThreads()
    {
        return Response::where('campaign_id', $this->campaign->id)
                ->where('target_id', $this->recipient->id)
                ->where('type', 'text')
                ->get()
                ->toArray();
    }

    private function getPhoneThreads()
    {
        return Response::where('campaign_id', $this->campaign->id)
                ->where('target_id', $this->recipient->id)
                ->where('type', 'phone')
                ->get()
                ->toArray();
    }
}

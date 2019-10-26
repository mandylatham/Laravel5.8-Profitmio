<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CampaignUserScoreChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $broadcastQueue = 'pusher-queue';
    public $campaign;
    public $score;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CampaignUserScoreChanged $score, Campaign $campaign)
    {
        $this->campaign = $campaign;
        $this->score = $score;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
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
        return 'users.score-updated';
    }

    public function broadcastWith()
    {
        $scores = [];

        // @todo switch this out with CampaignUserScores
        foreach ($this->campaign->users as $user) {
            $scores[$user->name] = $this->score->forUser($user);
        }

        return $scores;
    }
}

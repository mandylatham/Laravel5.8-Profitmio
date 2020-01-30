<?php

namespace App\Events;

use App\Models\Campaign;
use App\Models\Recipient;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\DB;

/**
 * This event emit the total of campaign's responses (unread, with labels, phone, sms, etc)
 * Class CampaignCountsUpdated
 * @package App\Events
 */
class CampaignCountsUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public $broadcastQueue = 'pusher-queue';

    /** @var  Campaign */
    private $campaign;

    /** @var  int */
    private $total;
    private $new;
    private $open;
    private $closed;
    private $calls;
    private $emails;
    private $sms;

    /** @var  array */
    private $labelCounts;

    /**
     * Create a new event instance.
     *
     * @param $campaign Campaign
     */
    public function __construct(Campaign $campaign)
    {
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
        return 'counts.updated';
    }

    public function broadcastWith()
    {
        $counters = [];
        $counters['total'] = Recipient::withResponses($this->campaign->id)->count();
        $counters['new'] = $this->campaign->leads()->new()->count();
        $counters['open'] = $this->campaign->leads()->open()->count();
        $counters['closed'] = $this->campaign->leads()->closed()->count();
        $counters['calls'] = $this->campaign->leads()->whereHas('responses', function ($q) { $q->whereType('phone'); })->count();
        $counters['email'] = $this->campaign->leads()->whereHas('responses', function ($q) { $q->whereType('email'); })->count();
        $counters['sms'] = $this->campaign->leads()->whereHas('responses', function ($q) { $q->whereType('text'); })->count();

        return $counters;
    }
}

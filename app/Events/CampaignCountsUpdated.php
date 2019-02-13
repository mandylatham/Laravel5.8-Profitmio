<?php

namespace App\Events;

use DB;
use App\Models\Campaign;
use App\Models\Recipient;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

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
    private $totalCount;
    private $unread;
    private $idle;
    private $archived;
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
        $counters['unread'] = Recipient::unread($this->campaign->id)->count();
        $counters['idle'] = Recipient::idle($this->campaign->id)->count();
        $counters['calls'] = Recipient::withResponses($this->campaign->id)->whereIn(
            'recipients.id',
            result_array_values(
                DB::select("select recipient_id from responses where campaign_id = {$this->campaign->id} and type='phone'")
            )
        )->count();
        $counters['email'] = Recipient::withResponses($this->campaign->id)->whereIn(
            'recipients.id',
            result_array_values(
                DB::select("select recipient_id from responses where campaign_id = {$this->campaign->id} and type='email'")
            )
        )->count();
        $counters['sms'] = Recipient::withResponses($this->campaign->id)->whereIn(
            'recipients.id',
            result_array_values(
                DB::select("select recipient_id from responses where campaign_id = {$this->campaign->id} and type='text'")
            )
        )->count();

        $labels = ['none', 'interested', 'appointment', 'callback', 'service', 'not_interested', 'wrong_number', 'car_sold', 'heat'];
        foreach ($labels as $label) {
            $counters[$label] = Recipient::withResponses($this->campaign->id)
                ->labelled($label, $this->campaign->id)
                ->count();
        }
        return $counters;
    }
}

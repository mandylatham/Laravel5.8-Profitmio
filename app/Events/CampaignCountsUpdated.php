<?php

namespace App\Events;

use App\Models\Campaign;
use App\Models\Recipient;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

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

        $this->totalCount = Recipient::withResponses($campaign->id)->count();
        $this->unread = Recipient::unread($campaign->id)->count();
        $this->idle = Recipient::idle($campaign->id)->count();
        $this->archived = Recipient::archived()->count();
        $this->emails = Recipient::withResponses($campaign->id)->whereIn(
            'recipients.id',
            result_array_values(
                \DB::select("select recipient_id from responses where campaign_id = {$campaign->id} and type='email'")
            )
        )->count();
        $this->calls = Recipient::withResponses($campaign->id)->whereIn(
            'recipients.id',
            result_array_values(
                \DB::select("select recipient_id from responses where campaign_id = {$campaign->id} and type='phone'")
            )
        )->count();
        $this->sms = Recipient::withResponses($campaign->id)->whereIn(
            'recipients.id',
            result_array_values(
                \DB::select("select recipient_id from responses where campaign_id = {$campaign->id} and type='text'")
            )
        )->count();
        $labelCounts = Recipient::withResponses($campaign->id)
                                      ->selectRaw("sum(interested) as interested, sum(not_interested) as not_interested,
                sum(appointment) as appointment, sum(service) as service, sum(wrong_number) as wrong_number,
                sum(car_sold) as car_sold, sum(heat) as heat_case, sum(callback) as callback, 
                sum(case when (interested = 0 and not_interested = 0 and appointment = 0 and service = 0 and
                wrong_number = 0 and car_sold = 0 and heat = 0 and callback = 0) then 1 else 0 end) as not_labelled")
                                      ->first();
        $this->labelCounts = [
            'not_labelled' => $labelCounts->not_labelled,
            'interested' => $labelCounts->interested,
            'appointment' => $labelCounts->appointment,
            'callback' => $labelCounts->callback,
            'service' => $labelCounts->service,
            'not_interested' => $labelCounts->not_interested,
            'wrong_number' => $labelCounts->wrong_number,
            'car_sold' => $labelCounts->car_sold,
            'heat_case' => $labelCounts->heat_case,
        ];

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
        return [
            'totalCount' => $this->totalCount,
            'unread' => $this->unread,
            'idle' => $this->idle,
            'archived' => $this->archived,
            'calls' => $this->calls,
            'emails' => $this->emails,
            'sms' => $this->sms,
            'labelCounts' => $this->labelCounts,
        ];
    }
}

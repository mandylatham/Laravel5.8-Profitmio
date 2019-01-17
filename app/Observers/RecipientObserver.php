<?php

namespace App\Observers;

use App\Models\Recipient;
use Pusher\Pusher;

class RecipientObserver
{
    /**
     * Handle the recipient "created" event.
     *
     * @param Recipient $recipient
     * @return void
     * @throws \Pusher\PusherException
     */
    public function created(Recipient $recipient)
    {
        $labelCounts = $recipient::with('responses')
            ->selectRaw("sum(interested) as interested, sum(not_interested) as not_interested,
                sum(appointment) as appointment, sum(service) as service, sum(wrong_number) as wrong_number,
                sum(car_sold) as car_sold, sum(heat) as heat_case, sum(callback) as callback,
                sum(case when (interested = 0 and not_interested = 0 and appointment = 0 and service = 0 and
                wrong_number = 0 and car_sold = 0 and heat = 0) then 1 else 0 end) as not_labelled")
            ->first();

        $data = [
            'labelCounts' => array_map('intval', $labelCounts->toArray()),
        ];

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_CLUSTER'),
                'useTLS'  => true,
            ]
        );

        $pusher->trigger('response-console', "counters.update.{$recipient->campaign_id}", $data);
    }

    /**
     * Handle the recipient "updated" event.
     *
     * @param Recipient $recipient
     * @return void
     * @throws \Pusher\PusherException
     */
    public function updated(Recipient $recipient)
    {
        $labelCounts = $recipient::with('responses')
            ->selectRaw("sum(interested) as interested, sum(not_interested) as not_interested,
                sum(appointment) as appointment, sum(service) as service, sum(wrong_number) as wrong_number,
                sum(car_sold) as car_sold, sum(heat) as heat_case, sum(callback) as callback,
                sum(case when (interested = 0 and not_interested = 0 and appointment = 0 and service = 0 and
                wrong_number = 0 and car_sold = 0 and heat = 0) then 1 else 0 end) as not_labelled")
            ->first();

        $data = [
            'labelCounts' => array_map('intval', $labelCounts->toArray()),
        ];

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_CLUSTER'),
                'useTLS'  => true,
            ]
        );

        $pusher->trigger('response-console', "counters.update.{$recipient->campaign_id}", $data);
    }

    /**
     * Handle the recipient "deleted" event.
     *
     * @param Recipient $recipient
     * @return void
     */
    public function deleted(Recipient $recipient)
    {
        //
    }

    /**
     * Handle the recipient "restored" event.
     *
     * @param Recipient $recipient
     * @return void
     */
    public function restored(Recipient $recipient)
    {
        //
    }

    /**
     * Handle the recipient "force deleted" event.
     *
     * @param Recipient $recipient
     * @return void
     */
    public function forceDeleted(Recipient $recipient)
    {
        //
    }
}

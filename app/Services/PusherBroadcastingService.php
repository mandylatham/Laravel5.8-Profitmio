<?php

namespace App\Services;

use App\Models\Recipient;
use Pusher\Pusher;

class PusherBroadcastingService
{
    // TODO: remove this class when not in use anymore
    /**
     * @param Recipient $recipient
     * @param array     $data
     * @throws \Pusher\PusherException
     */
    public static function broadcastRecipientResponseUpdated(Recipient $recipient, array $data = [])
    {
        if (empty($data)) {
            $data = [
                'appointments',
                'emails',
                'texts',
                'calls',
                'recipient',
                'labels'
            ];
        }

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_CLUSTER'),
                'useTLS'  => true,
            ]
        );

        $pusher->trigger("private-campaign.{$recipient->campaign->id}", "response.{$recipient->id}.updated", $data);
    }
}
<?php

namespace App\Listeners;

use App\Events\CampaignDropQueued;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendClientDropEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CampaignDropQueued  $event
     * @return void
     */
    public function handle(CampaignDropQueued $event)
    {
        echo $event->client->email;
    }
}

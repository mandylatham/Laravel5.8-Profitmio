<?php

namespace App\Classes;

use App\CampaignUser;
use App\User;

class CampaignUserActivityLog
{
    private const MESSAGE_ATTACHED = 'User %d assigned to campaign %d';
    private const MESSAGE_DETACHED = 'User %d de-assigned from campaign %d';

    public function attach(User $user, int $campaignId): void
    {
        $properties = [
            'user_id' => $user->id,
            'campaign_id' => $campaignId,
        ];
        $pivot = new CampaignUser();
        $pivot->id = $user->id;
        $logMessage = sprintf(self::MESSAGE_ATTACHED, $user->id, $campaignId);
        activity()
            ->performedOn($pivot)
            ->withProperties($properties)
            ->log($logMessage);
    }

    public function detach(User $user, int $campaignId): void
    {
        $properties = [
            'user_id' => $user->id,
            'campaign_id' => $campaignId,
        ];
        $pivot = new CampaignUser();
        $pivot->id = $user->id;
        $logMessage = sprintf(self::MESSAGE_DETACHED, $user->id, $campaignId);
        activity()
            ->performedOn($pivot)
            ->withProperties($properties)
            ->log($logMessage);
    }

    public function sync(User $user, array $changes): void
    {
        foreach($changes['attached'] as $campaignId) {
            $this->attach($user, $campaignId);
        }
        foreach($changes['detached'] as $campaignId) {
            $this->detach($user, $campaignId);
        }
    }
}

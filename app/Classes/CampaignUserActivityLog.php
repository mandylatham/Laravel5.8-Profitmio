<?php

namespace App\Classes;

use App\Models\Campaign;
use App\Models\CampaignUser;
use App\Models\User;

class CampaignUserActivityLog
{
    private const MESSAGE_ATTACHED = 'User %d (%s) assigned to campaign %d (%s)';
    private const MESSAGE_DETACHED = 'User %d (%s) de-assigned from campaign %d (%s)';

    public function attach(User $user, int $campaignId): void
    {
        $properties = [
            'user_id' => $user->id,
            'campaign_id' => $campaignId,
        ];
        $pivot = new CampaignUser();
        $pivot->id = $user->id;
        $campaign = Campaign::find($campaignId);
        $logMessage = sprintf(self::MESSAGE_ATTACHED, $user->id, $user->name, $campaignId, $campaign->name);
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
        $campaign = Campaign::find($campaignId);
        $logMessage = sprintf(self::MESSAGE_DETACHED, $user->id, $user->name, $campaignId, $campaign->name);
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

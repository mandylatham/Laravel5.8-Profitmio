<?php

namespace App\Services\CampaignUserScoreService\Models;

use App\Models\User;
use App\Models\Campaign;
use Illuminate\Support\Collection;

/**
 * Object for tracking user scores
 */
class UserScores
{
    /**
     * @var Campaign
     */
    private $campaign;

    /**
     * @var array
     */
    private $scores = [];

    /**
     * Constructor.
     *
     * @param Campaign $campaign
     */
    public function __construct(Campaign $campaign)
    {
        foreach ($campaign->users as $user)
        {
            $this->scores[$user->id] = 0;
        }
    }

    /**
     * Add points for a user
     *
     * @param User $user
     * @param int  $points
     *
     * @return void
     */
    public function addPointsToUser(User $user, int $points) : void
    {
        $this->scores[$user->id] = $this->scores[$user->id] + $points;
    }

    /**
     * Remove points for a user
     *
     * @param User $user
     * @param int  $points
     *
     * @return void
     */
    public function removePointsFromUser(User $user, int $points) : void
    {
        $this->scores[$user->id] = $this->scores[$user->id] - $points;
    }
}

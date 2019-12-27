<?php

namespace App\Factories;

use App\Models\CampaignUserScore;
use Spatie\Activitylog\Models\Activity;

class CampaignUserScoreFactory
{
    /**
     * @param Activity $activity
     * @param integer $score
     * @param integer $delta
     * @return CampaignUserScore
     */
    public function createFromActivity(Activity $activity, int $delta) : CampaignUserScore
    {
        return $this->create(
            $activity->subject->campaign_id,
            $activity->causer->id,
            $activity->id,
            $delta
        );
    }

    /**
     * @param array $params
     * @return CampaignUserScore
     */
    public function create(int $campaign_id, int $user_id, int $activity_id, int $delta) : CampaignUserScore
    {
        $lastTotal = CampaignUserScore::getLastScore($campaign_id, $user_id);
        $score = $lastTotal + $delta;

        return CampaignUserScore::create([
            'campaign_id' => $campaign_id,
            'user_id' => $user_id,
            'activity_id' => $activity_id,
            'score' => $score,
            'delta' => $delta,
        ]);
    }
}

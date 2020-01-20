<?php

namespace App\Services;

use App\Models\LeadActivity;
use App\Models\CampaignUserScore;
use App\Factories\ActivityLogFactory;
use Spatie\Activitylog\Models\Activity;
use App\Factories\CampaignUserScoreFactory;

class CampaignUserScoreService
{
    /** @var bool */
    const ENFORCE_PENALTIES = true;

    /** @var int */
    const FALSE_OPEN_PENALTY = 25;

    /** @var CampaignUserScoreFactory */
    private $score;

    /**
     * @var array
     * */
    private $scoreMap = [
        LeadActivity::OPENED => [
            'good' => 7200,
            'goodPoints' => 75,
            'ok' => 28800,
            'okPoints' => 25,
            'bad' => 86400,
            'badPoints' => 5,
        ],
        LeadActivity::CLOSED => [
            'good' => 259200,
            'goodPoints' => 25,
            'ok' => 432000,
            'okPoints' => 10,
            'bad' => 604800,
            'badPoints' => 5,
        ],
        LeadActivity::SENTEMAIL => ['points' => 1],
        LeadActivity::SENTSMS => ['points' => 1],
        LeadActivity::SENTTOCRM => ['points' => 5],
        LeadActivity::SENTTOSERVICE => ['points' => 5],
        LeadActivity::ADDEDAPPOINTMENT => ['points' => 10],
        LeadActivity::CALLEDBACK => ['points' => 10],
        LeadActivity::CHECKED_IN => ['points' => 30],
    ];

    /** @var array */
    private $inertActivities = [
        LeadActivity::MARKETED,
        LeadActivity::VIEWED,
    ];

    /**
     * @param CampaignUserScoreFactory $score
     */
    public function __construct(CampaignUserScoreFactory $score)
    {
        $this->score = $score;
    }

    /**
     * @param Activity $activity
     * @return void
     */
    public function forActivity(Activity $activity) : void
    {
        if (!$this->isScorable($activity)) return;

        // If the lead was reopened, previous closing points are removed from that user
        if ($activity->action == LeadActivity::REOPENED) {
            $this->removeClosedPoints($activity);
        }

        $this->penalizeFalseOpen($activity);

        $this->addActivityPoints($activity);
    }

    /**
     * @param Activity $activity
     * @return integer
     */
    private function getPoints(Activity $activity): int
    {
        $pointMap = $this->scoreMap[$activity->description];

        $seconds = $this->getSeconds($activity);

        if (is_null($seconds)) {
            return $pointMap['points'];
        }
        if ($seconds <= $pointMap['good']) {
            return $pointMap['goodPoints'];
        }
        if ($seconds <= $pointMap['ok']) {
            return $pointMap['okPoints'];
        }
        return $pointMap['badPoints'];
    }

    /**
     * @param Activity $activity
     * @return void
     */
    private function addActivityPoints(Activity $activity): void
    {
        $points = $this->getPoints($activity);

        $this->score->createFromActivity($activity, $points);
    }

    /**
     * Get the number of seconds since the last activity
     * @param Activity $activity
     * @return ?int
     */
    private function getSeconds(Activity $activity): ?int
    {
        switch ($activity->description) {
            case LeadActivity::OPENED:
            case LeadActivity::CLOSED:
                return $activity->subject->responses()->first()->created_at->diffInSeconds($activity->activity_at);
                break;
        }

        return null;
    }

    /**
     * Remove closed points for closer
     * @param Activity $activity
     * @return void
     * @throws \Exception
     */
    private function removeClosedPoints(Activity $activity): void
    {
        $lastClose = $activity->subject->activities()
            ->whereStatus(LeadActivity::CLOSED)
            ->orderBy('id', 'desc')
            ->firstOrFail();

        $lastCloseScore = CampaignUserScore::whereActivityId($lastClose->id)
            ->firstOrFail();

        $closePoints = $lastCloseScore->delta;
        $delta = 0 - abs($closePoints);

        $this->score->create(
            $activity->subject->campaign_id,
            $lastClose->user_id,
            $activity->id, $delta);
    }

    /**
     * @param Activity $activity
     * @return void
     */
    private function penalizeFalseOpen(Activity $activity): void
    {
        // Get last activity
        $lastActivity = Activity::whereLogName(ActivityLogFactory::LEAD_ACTIVITY_LOG)
            ->whereSubjectType($activity->subject_type)
            ->whereSubjectId($activity->subject_id)
            ->where('id', '<>', $activity->id)
            ->whereNotIn('description', $this->inertActivities)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastActivity
            && $lastActivity->description === LeadActivity::OPENED
            && $lastActivity->causer_id !== $activity->causer_id
            && !in_array($activity->description, $this->inertActivities)
        ) {
            $campaign_id = $activity->subject->campaign_id;

            $lastActivityScoreRecord = CampaignUserScore::getScoreRecordFromActivity($lastActivity);

            $pointsToRemove = $lastActivityScoreRecord->delta;

            // Remove points from false opener's user
            $this->score->create(
                $campaign_id,
                $lastActivity->causer_id,
                $activity->id,
                -$pointsToRemove
            );

            // award false opener's points to this user
            $this->score->create(
                $campaign_id,
                $activity->causer_id,
                $activity->id,
                $pointsToRemove
            );
        }
    }

    /**
     * @param Activity $activity
     * @return boolean
     */
    private function isScorable(Activity $activity): bool
    {
        return array_key_exists($activity->description, $this->scoreMap);
    }
}

<?php

namespace App\Service;

use App\Models\User;
use App\Models\Campaign;
use App\Models\RecipientActivity;

class CampaignUserScoreService
{
    const FALSE_OPEN_PENALTY = 25;

    private $scores = [];
    private $scoreMap = [
        RecipientActivity::OPENED => [
            'good' => 7200,
            'goodPoints' => 75,
            'ok' => 28800,
            'okPoints' => 25,
            'bad' => 86400,
            'badPoints' => 5,
        ],
        RecipientActivity::CLOSED => [
            'good' => 259200,
            'goodPoints' => 25,
            'ok' => 432000,
            'okPoints' => 10,
            'bad' => 604800,
            'badPoints' => 5,
        ],
        RecipientActivity::SENTEMAIL => ['points' => 1],
        RecipientActivity::SENTSMS => ['points' => 1],
        RecipientActivity::SENTTOCRM => ['points' => 5],
        RecipientActivity::SENTTOSERVICE => ['points' => 5],
        RecipientActivity::ADDAPPOINTMENT => ['points' => 10],
    ];

    public function forUser(Campaign $campaign, User $user)
    {
        $score = 0;
        $closedScore = 0;
        $activities = $campaign->leads()->activity()->whereUserId($user->id)->get();

        foreach ($activities as $activity) {
            if ($activity->action == RecipientActivity::REOPENED) {
                $closedScore = 0;
            }
            if (array_key_exists($activity->action, $this->scoreMap)) {
                $firstOutboundResponse = $activity->recipient()->responses()->whereIncoming(0)->first();

                if ($activity->action == RecipientActivity::CLOSED) {
                    $closedScore = $this->getPoints($activity);
                }

                // If the user opened a lead, but someone else responsded first, they get penalized
                if ($activity->action == RecipientActivity::OPENED) {
                    if ($firstOutboundResponse && $firstOutboundResponse->user_id !== $user->id) {
                        $score -= self::FALSE_OPEN_PENALTY;
                        continue;
                    }
                }

                // If a first responder on an open lead, they get the opener's points
                if (in_array($activity->action, [RecipientActivity::SENTSMS, RecipientActivity::SENTEMAIL])) {
                    if ($firstOutboundResponse && $firstOutboundResponse->user_id == $user->id) {
                        $activity->action = RecipientActivity::OPENED;
                    }
                }

                $score += $this->getPoints($activity);
            }
        }

        return $score + $closedScore;
    }

    private function getPoints(RecipientActivity $activity)
    {
        $pointMap = $this->scoreMap[$activity->action];
        $seconds = $this->getSeconds($activity);

        if (!$seconds) {
            return $pointMap['points'];
        }

        if ($seconds <= $pointMap['good']) {
            return $pointMap['goodPoints'];
        }
        if ($seconds <= $pointMap['ok']) {
            return $pointMap['okPoints'];
        }
        if ($seconds <= $pointMap['bad']) {
            return $pointMap['badPoints'];
        }
        return 0;
    }

    private function getSeconds(RecipientActivity $activity)
    {
        switch ($activity->action) {
        case RecipientActivity::OPENED:
            return $activity->metadata['seconds_since_last_activity'];
            break;
        case RecipientActivity::CLOSED:
            return $activity->recipient()->responses()->first()->created_at->diffInSeconds($activity->activity_at);
            break;
        }

        return null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class CampaignUserScore extends Model
{
    /**
     * Disable guarding
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Add points for this user
     *
     * @param int $points
     *
     * @return void
     */
    public function addPoints(int $points) : void
    {
        $this->points = $this->points + $points;

        $this->save();
    }

    /**
     * Remove points for this user
     *
     * @param int $points
     *
     * @return void
     */
    public function removePoints(int $points) : void
    {
        $this->points = $this->points - $points;

        $this->save();
    }

    /**
     * @param Activity $activity
     * @return integer
     */
    public static function getLastScoreFromActivity(Activity $activity) : int
    {
        return self::getLastScore($activity->subject->campaign_id, $activity->causer->id);
    }

    public static function getScoreRecordFromActivity(Activity $activity)
    {
        return self::where('activity_id', $activity->id)
            ->where('user_id', $activity->causer_id)
            ->first();
    }

    /**
     * @param integer $campaign_id
     * @param integer $user_id
     * @return integer
     */
    public static function getLastScore(int $campaign_id, int $user_id) : int
    {
        $lastScoreRecord = self::whereCampaignId($campaign_id)
            ->whereUserId($user_id)
            ->orderBy('id', 'desc')
            ->first();

        if (! $lastScoreRecord) {
            return 0;
        }

        return $lastScoreRecord->score;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

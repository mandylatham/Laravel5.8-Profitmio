<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\CampaignUserScoreService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CalculateCampaignUserScore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $campaign;
    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Campaign $campaign, User $user)
    {
        $this->campaign = $campaign;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @todo Write the score to the CampaignUserScore record
     *
     * @return void
     */
    public function handle(CampaignUserScoreService $score)
    {
        $score = $score->forUser($this->campaign, $this->user);
    }
}

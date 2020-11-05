<?php

namespace App\Console\Commands;

use App\Services\FacebookService;
use App\Models\GlobalSettings;
use Carbon\Carbon;
use App\Mail\RenewFacebookTokenNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;

class RenewFacebookToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook-integration:reminder-renew-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder for renew facebook token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $globalSettings = GlobalSettings::where('name', 'facebook_access_token')->first() ?? (object) ['value' => null];
        $access_token = $globalSettings->value;

        $facebookService = new FacebookService();
        $tokenExpiresAt = $facebookService->getTokenExpiresAt($access_token);

        if($tokenExpiresAt){
            $expiresAt = new Carbon($tokenExpiresAt);
            $daysToExpire = $expiresAt->diffInDays();
            if($daysToExpire === 7 || $daysToExpire === 3 || $daysToExpire === 2 || $daysToExpire === 1){
                Mail::to('rbeecher@profitminer.io')->send(new RenewFacebookTokenNotification($tokenExpiresAt));
            }
        }
    }
}

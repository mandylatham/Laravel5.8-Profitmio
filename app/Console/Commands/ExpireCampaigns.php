<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use Illuminate\Console\Command;

class ExpireCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaigns:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform the expiration of campaigns';

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
        Campaign::where('status', '<>', 'Expired')->whereRaw("coalesce(expires_at, '1900-01-01') <= current_timestamp")->update(['status' => 'Expired']);
    }
}

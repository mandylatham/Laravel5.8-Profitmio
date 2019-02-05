<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use DB;
use Illuminate\Console\Command;

class MigrateCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:campaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate campaigns';

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
        $this->info('====== Migrating campaign table ==============');
        Campaign::truncate();
        $size = 250;
        $bar = $this->output->createProgressBar(DB::connection('mysql_legacy')->table('campaigns')->select('*')->count());
        $bar->start();

        DB::connection('mysql_legacy')->table('campaigns')->orderBy('campaign_id')->chunk($size, function($campaigns) use ($bar) {
            $insert = [];
            foreach ($campaigns as $campaign) {
                $trans = (array) $campaign;
                $trans['id'] = $trans['campaign_id'];
                unset($trans['campaign_id']);
                $trans['dealership_id'] = $trans['client_id'];
                unset($trans['client_id']);
                $bar->advance();
                $insert[] = $trans;
            }
            Campaign::insert($insert);
        });
        $bar->finish();
        $this->info("\n====== Migration finished ==============");
    }
}

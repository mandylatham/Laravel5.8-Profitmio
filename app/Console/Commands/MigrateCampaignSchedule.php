<?php

namespace App\Console\Commands;

use App\Models\Drop;
use DB;
use Illuminate\Console\Command;

class MigrateCampaignSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:campaign-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate campaign schedule';

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
        $this->info('====== Migrating campaign_schedules table ==============');
        Drop::truncate();
        $size = 250;
        $bar = $this->output->createProgressBar(DB::connection('mysql_legacy')->table('campaign_schedules')->select('*')->count());
        $bar->start();

        DB::connection('mysql_legacy')->table('campaign_schedules')->orderBy('campaign_schedule_id')->chunk($size, function($campaignSchedules) use ($bar) {
            $insert = [];
            foreach ($campaignSchedules as $campaignSchedule) {
                $trans = (array) $campaignSchedule;
                // Remove campaign_schedule_Id
                $trans['id'] = $trans['campaign_schedule_id'];
                unset($trans['campaign_schedule_id']);
                // Remove target_group
                $trans['recipient_group'] = $trans['target_group'];
                unset($trans['target_group']);
                $bar->advance();
                $insert[] = $trans;
            }
            Drop::insert($insert);
        });
        $bar->finish();
        $this->info("\n====== Migration finished ==============");
    }
}

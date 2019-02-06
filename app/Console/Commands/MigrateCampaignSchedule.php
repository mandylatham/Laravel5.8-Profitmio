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
    protected $signature = 'pm-import:campaign-schedule';

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
        $this->info('Campaign schedule migration started.');
        Drop::truncate();
        DB::insert('insert into profitminer.campaign_schedules
(id, campaign_id, recipient_group, type, email_subject, email_html, email_text, text_message, text_message_image, send_vehicle_image, status, percentage_complete, system_id, send_at, started_at, completed_at, notified_at, created_at, updated_at, deleted_at)
select campaign_schedule_id, campaign_id, target_group, type, email_subject, email_html, email_text, text_message, text_message_image, send_vehicle_image, status, percentage_complete, system_id, send_at, started_at, completed_at, notified_at, created_at, updated_at, deleted_at from profitminer_original_schema.campaign_schedules;');
        $this->info("Campaigns migration completed.");
    }
}

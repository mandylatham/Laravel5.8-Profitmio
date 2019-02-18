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
    protected $signature = 'pm-import:campaign';

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
        $this->info('Campaigns migration started.');
        Campaign::truncate();
        DB::insert('insert into profitminer.campaigns
(id, agency_id, dealership_id, name, expires_at, order_id, sms_on_callback, sms_on_callback_number, service_dept, service_dept_email, adf_crm_export, adf_crm_export_email, lead_alerts, lead_alert_email, client_passthrough, client_passthrough_email, starts_at, ends_at, status, created_at, updated_at, deleted_at)
select campaign_id, agency_id, client_id, name, expires_at, order_id, sms_on_callback, sms_on_callback_number, service_dept, service_dept_email, adf_crm_export, adf_crm_export_email, lead_alerts, lead_alert_email, client_passthrough, client_passthrough_email, starts_at, ends_at, status, created_at, updated_at, deleted_at from profitminer_original_schema.campaigns;');
        $this->info("Campaigns migration completed.");
    }
}

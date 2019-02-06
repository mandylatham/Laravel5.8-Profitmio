<?php

namespace App\Console\Commands;

use App\Models\SmsSuppression;
use DB;
use Illuminate\Console\Command;

class MigrateSmsSuppression extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pm-import:sms-suppression';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate sms suppression';

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
        $this->info('Sms Suppressions migration started.');
        SmsSuppression::truncate();
        DB::insert('insert into profitminer.sms_suppressions
(phone, suppressed_at)
select phone, suppressed_at from profitminer_original_schema.sms_suppressions;');
        $this->info('Sms Suppresions migration completed.');
    }
}

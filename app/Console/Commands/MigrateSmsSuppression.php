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
    protected $signature = 'migrate:sms-suppression';

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
        $this->info('====== Migrating sms-suppression table ==============');
        SmsSuppression::truncate();
        $size = 250;
        $bar = $this->output->createProgressBar(DB::connection('mysql_legacy')->table('sms_suppressions')->select('*')->count());
        $bar->start();

        DB::connection('mysql_legacy')->table('sms_suppressions')->orderBy('phone')->chunk($size, function($phones) use ($bar) {
            $insert = [];
            foreach ($phones as $phone) {
                $trans = (array) $phone;
                $bar->advance();
                $insert[] = $trans;
            }
            SmsSuppression::insert($insert);
        });
        $bar->finish();
        $this->info("\n====== Migration finished ==============");
    }
}

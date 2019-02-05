<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use DB;
use Illuminate\Console\Command;

class MigrateAppointment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:appointment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate appointment';

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
        $this->info('====== Migrating appointment table ==============');
        Appointment::truncate();
        $size = 250;
        $bar = $this->output->createProgressBar(DB::connection('mysql_legacy')->table('appointments')->select('*')->count());
        $bar->start();

        DB::connection('mysql_legacy')->table('appointments')->orderBy('appointment_id')->chunk($size, function($appointments) use ($bar) {
            $insert = [];
            foreach ($appointments as $appointment) {
                $trans = (array) $appointment;
                // Remove appointment_id
                $trans['id'] = $trans['appointment_id'];
                unset($trans['appointment_id']);
                // Remove target_id
                $trans['recipient_id'] = $trans['target_id'];
                unset($trans['target_id']);
                $bar->advance();
                $insert[] = $trans;
            }
            Appointment::insert($insert);
        });
        $bar->finish();
        $this->info("\n====== Migration finished ==============");
    }
}

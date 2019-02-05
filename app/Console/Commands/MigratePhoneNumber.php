<?php

namespace App\Console\Commands;

use App\Models\PhoneNumber;
use DB;
use Illuminate\Console\Command;

class MigratePhoneNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:phone-number';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate phone number';

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
        $this->info('====== Migrating phone_number table ==============');
        PhoneNumber::truncate();
        $size = 250;
        $bar = $this->output->createProgressBar(DB::connection('mysql_legacy')->table('phone_numbers')->select('*')->count());
        $bar->start();

        DB::connection('mysql_legacy')->table('phone_numbers')->orderBy('phone_number_id')->chunk($size, function($phones) use ($bar) {
            $insert = [];
            foreach ($phones as $phone) {
                $trans = (array) $phone;
                $trans['id'] = $trans['phone_number_id'];
                unset($trans['phone_number_id']);
                $trans['dealership_id'] = $trans['client_id'];
                unset($trans['client_id']);
                $bar->advance();
                $insert[] = $trans;
            }
            PhoneNumber::insert($insert);
        });
        $bar->finish();
        $this->info("\n====== Migration finished ==============");
    }
}

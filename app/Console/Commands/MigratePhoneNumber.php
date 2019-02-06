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
    protected $signature = 'pm-import:phone-number';

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
        $this->info('Phone number migration started.');
        PhoneNumber::truncate();
        DB::insert('insert into profitminer.phone_numbers
(id, dealership_id, phone_number, campaign_id, call_source_name, forward, sid, created_at, updated_at, deleted_at)
select phone_number_id, client_id, phone_number, campaign_id, call_source_name, forward, sid, created_at, updated_at, deleted_at from profitminer_original_schema.phone_numbers;');
        $this->info('Phone number migration completed.');
    }
}

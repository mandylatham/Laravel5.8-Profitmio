<?php

namespace App\Console\Commands;

use App\Models\Recipient;
use DB;
use Illuminate\Console\Command;

class MigrateRecipient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pm-import:recipient';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate recipients';

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
        $this->info('Recipients migration started.');
        Recipient::truncate();
        DB::insert('insert into profitminer.recipients
(id, campaign_id, recipient_list_id, unique_recipient_id, first_name, last_name, email, phone, address1, address2, city, state, zip, year, make, model, vin, carrier, carrier_type, subgroup, service, appointment, heat, interested, not_interested, wrong_number, car_sold, callback, email_valid, phone_valid, from_dealer_db, notes, archived_at, last_responded_at, created_at, updated_at, deleted_at)
select target_id, campaign_id, recipient_list_id, unique_recipient_id, first_name, last_name, email, phone, address1, address2, city, state, zip, year, make, model, vin, carrier, carrier_type, subgroup, service, appointment, heat, interested, not_interested, wrong_number, car_sold, callback, email_valid, phone_valid, from_dealer_db, notes, archived_at, last_responded_at, created_at, updated_at, deleted_at from profitminer_original_schema.targets;');
        $this->info('Recipients migration completed.');
    }
}

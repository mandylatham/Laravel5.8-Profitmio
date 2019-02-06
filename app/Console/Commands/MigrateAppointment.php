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
    protected $signature = 'pm-import:appointment';

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
        $this->info('Appointments migration started.');
        Appointment::truncate();
        DB::insert('insert into profitminer.appointments
(id, campaign_id, recipient_id, appointment_at, first_name, last_name, phone_number, alt_phone_number, email, address, city, state, zip, auto_year, auto_make, auto_model, auto_trim, auto_mileage, type, called_back, created_at, updated_at, deleted_at)
select appointment_id, campaign_id, target_id, appointment_at, first_name, last_name, phone_number, alt_phone_number, email, address, city, state, zip, auto_year, auto_make, auto_model, auto_trim, auto_mileage, type, called_back, created_at, updated_at, deleted_at from profitminer_original_schema.appointments;');
        $this->info("Appointments migration completed.");
    }
}

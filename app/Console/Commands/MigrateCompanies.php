<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\User;
use DB;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MigrateCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pm-import:companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate companies';

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
        $this->info('Companies migration started.');
        Company::truncate();
        User::truncate();
        CompanyUser::truncate();
        $size = 250;
        $users = DB::connection('mysql_legacy')
            ->table('users')
            ->orderBy('id')
            ->where(function ($query) {
                $query->where('access', 'Agency')
                    ->orWhere('access', 'Client');
            });
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $users->chunk($size, function($users) use ($bar) {
            foreach ($users as $user) {
                // Create company
                $company = Company::firstOrCreate([
                    'name' => $user->organization,
                    'type' => $user->access === 'Agency' ? 'agency' : 'dealership'
                ]);
                if ($user->access === 'Agency') {
                    Campaign::where('agency_id', $user->id)->update([
                        'agency_id' => $company->id
                    ]);
                } else {
                    Campaign::where('dealership_id', $user->id)->update([
                        'dealership_id' => $company->id
                    ]);
                }
                $bar->advance();
            }
        });

        $support = Company::create([
            'name' => 'PM',
            'type' => 'support'
        ]);
        $user = new User();
        $user->first_name = 'Admin';
        $user->last_name = 'Profitminer';
        $user->email = 'admin@profitminer.io';
        $user->is_admin = true;
        $user->password = bcrypt('password');
        $user->save();

        $support->users()->save($user, [
            'completed_at' => Carbon::now()->toDateTimeString(),
            'config' => json_encode([
                'timezone' => 'US/Alaska'
            ]),
            'role' => 'admin'
        ]);

        $bar->finish();

        $this->info("\nCompanies migration completed.");
    }
}

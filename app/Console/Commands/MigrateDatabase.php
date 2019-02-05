<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate database';

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
        $this->call('migrate:campaign-schedule', []);
        $this->call('migrate:appointment', []);
        $this->call('migrate:campaign', []);
        $this->call('migrate:phone-number', []);
        $this->call('migrate:recipient', []);
        $this->call('migrate:recipient-list', []);
        $this->call('migrate:response', []);
//        $this->call('migrate:sms-suppression', []);
    }
}

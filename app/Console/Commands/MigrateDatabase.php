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
    protected $signature = 'pm-import:database';

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
        $this->call('pm-import:campaign-schedule', []);
        $this->call('pm-import:appointment', []);
        $this->call('pm-import:campaign', []);
        $this->call('pm-import:phone-number', []);
        $this->call('pm-import:recipient', []);
        $this->call('pm-import:recipient-list', []);
        $this->call('pm-import:response', []);
        $this->call('pm-import:sms-suppression', []);
    }
}

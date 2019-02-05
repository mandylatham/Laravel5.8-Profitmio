<?php

namespace App\Console\Commands;

use DB;
use App\Models\RecipientList;
use Illuminate\Console\Command;

class MigrateRecipientList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:recipient-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate recipient list';

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
        $this->info('====== Migrating recipient list table ==============');
        RecipientList::truncate();
        $size = 250;
        $bar = $this->output->createProgressBar(DB::connection('mysql_legacy')->table('recipient_lists')->select('*')->count());
        $bar->start();

        DB::connection('mysql_legacy')->table('recipient_lists')->orderBy('id')->chunk($size, function($recipientLists) use ($bar) {
            $insert = [];
            foreach ($recipientLists as $recipientList) {
                $trans = (array) $recipientList;
                $bar->advance();
                $insert[] = $trans;
            }
            RecipientList::insert($insert);
        });
        $bar->finish();
        $this->info("\n====== Migration finished ==============");
    }
}

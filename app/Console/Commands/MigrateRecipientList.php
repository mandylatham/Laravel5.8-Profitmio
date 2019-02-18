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
    protected $signature = 'pm-import:recipient-list';

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
        $this->info('Recipient lists migration started.');
        RecipientList::truncate();
        DB::insert('insert into profitminer.recipient_lists
(id, campaign_id, uploaded_by, name, fieldmap, email_validated, recipients_added, phones_validated, total_recipients, total_dealer_db, total_conquest, total_valid_phones, total_valid_emails, upload_identifier, type, failed_reason, failed_at, created_at, updated_at, deleted_at)
select id, campaign_id, uploaded_by, name, fieldmap, email_validated, recipients_added, phones_validated, total_recipients, total_dealer_db, total_conquest, total_valid_phones, total_valid_emails, upload_identifier, type, failed_reason, failed_at, created_at, updated_at, deleted_at from profitminer_original_schema.recipient_lists;');
        $this->info('Recipient lists migration completed.');
    }
}

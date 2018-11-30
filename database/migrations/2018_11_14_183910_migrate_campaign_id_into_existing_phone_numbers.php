<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateCampaignIdIntoExistingPhoneNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select("update campaigns 
            join phone_numbers on campaigns.phone_number_id = phone_numbers.id 
            set phone_numbers.campaign_id = campaigns.id 
            where campaigns.phone_number_id <> ''");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Do not remove campaign_ids. Rolling back the previous migration will accomplish this.
    }
}

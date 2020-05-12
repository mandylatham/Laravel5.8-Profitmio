<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCloudOneToCampaign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->boolean('enable_call_center')->nullable()->default(false);
            $table->string('cloud_one_campaign_id', 15)->nullable();
            $table->string('cloud_one_phone_number', 100)->nullabel();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->removeColumn('enable_call_center');
            $table->removeColumn('cloud_one_campaign_id');
            $table->removeColumn('cloud_one_phone_number');
        });
    }
}

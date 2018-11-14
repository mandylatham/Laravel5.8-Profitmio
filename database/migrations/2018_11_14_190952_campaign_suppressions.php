<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CampaignSuppressions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_suppressions', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('unique_recipient_id');
            $table->unsignedInteger('campaign_id');
            $table->unsignedInteger('suppressed_by');
            $table->unsignedInteger('deleted_by');
            $table->unique(['unique_recipient_id','campaign_id']);

            // adding created_at, updated_at, deleted_at columns
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('campaign_suppressions');
    }
}

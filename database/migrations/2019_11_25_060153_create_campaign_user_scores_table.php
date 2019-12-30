<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignUserScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_user_scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('campaign_id');
            $table->bigInteger('user_id');
            $table->bigInteger('activity_id');
            $table->integer('score')->default(0);
            $table->integer('delta')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_user_scores');
    }
}

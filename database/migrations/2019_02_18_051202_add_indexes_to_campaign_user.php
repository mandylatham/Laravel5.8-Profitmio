<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToCampaignUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_user', function (Blueprint $table) {
            $table->index('campaign_id', 'campaign_id_idx');
            $table->index('user_id', 'user_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_user', function (Blueprint $table) {
            $table->dropIndex('campaign_id_idx');
            $table->dropIndex('user_id_idx');
        });
    }
}

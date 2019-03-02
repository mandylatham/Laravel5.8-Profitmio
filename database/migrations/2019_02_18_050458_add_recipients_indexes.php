<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRecipientsIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipients', function (Blueprint $table) {
            $table->index('recipient_list_id', 'recipient_list_id_idx');
            $table->index('phone', 'phone_idx');
            $table->index('campaign_id', 'campaign_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipients', function (Blueprint $table) {
            $table->dropIndex('recipient_list_id_idx');
            $table->dropIndex('phone_idx');
            $table->dropIndex('campaign_id_idx');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToResponses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->index('campaign_id', 'campaign_id_idx');
            $table->index('recipient_id', 'recipient_id_idx');
            $table->index('type', 'type_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropIndex('campaign_id_idx');
            $table->dropIndex('recipient_id_idx');
            $table->dropIndex('type_idx');
        });
    }
}

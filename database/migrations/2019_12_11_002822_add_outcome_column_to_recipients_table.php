<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOutcomeColumnToRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipients', function (Blueprint $table) {
            if (! Schema::hasColumn('recipients', 'outcome')) {
                $table->string("outcome", 50)->nullable()->after("last_status_changed_at");
            }
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
            if (Schema::hasColumn('recipients', 'outcome')) {
                $table->dropColumn("outcome");
            }
        });
    }
}

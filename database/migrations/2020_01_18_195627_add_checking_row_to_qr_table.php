<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCheckingRowToQrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipient_text_to_value', function (Blueprint $table) {
            $table->boolean('value_requested')->default(false);
            $table->timestamp('value_requested_at')->nullable();
            $table->boolean('checked_in')->default(false);
            $table->timestamp('checked_in_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipient_text_to_value', function (Blueprint $table) {
            $table->dropColumn('value_requested');
            $table->dropColumn('value_requested_at');
            $table->dropColumn('checked_in');
            $table->dropColumn('checked_in_at');
        });
    }
}

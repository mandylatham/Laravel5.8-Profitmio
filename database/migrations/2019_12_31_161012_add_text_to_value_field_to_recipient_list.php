<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTextToValueFieldToRecipientList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipient_lists', function (Blueprint $table) {
            $table->boolean('text_to_value')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipient_lists', function (Blueprint $table) {
            $table->dropColumn('text_to_value');
        });
    }
}

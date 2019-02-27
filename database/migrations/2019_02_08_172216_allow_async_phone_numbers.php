<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllowAsyncPhoneNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('phone_numbers', function (Blueprint $table) {
            $table->integer('campaign_id')->nullable()->change();
            $table->string('forward', 50)->nullable()->change();

            $table->unique(['campaign_id', 'call_source_name'], 'phone_numbers_unique_source');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('phone_numbers', function (Blueprint $table) {
            $table->integer('campaign_id')->change();
            $table->string('forward', 50)->change();

            $table->dropUnique('phone_numbers_unique_source');
        });
    }
}

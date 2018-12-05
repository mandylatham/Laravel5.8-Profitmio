<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PhoneNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('dealership_id');
            $table->string('phone_number');
            $table->integer('campaign_id')->nullable();
            $table->string('call_source_name', 50)->nullable();
            $table->string('forward');
            $table->string('sid');
            $table->string('region');
            $table->string('state');
            $table->string('zip');

            // adding created_at, updated_at, deleted_at columns
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('phone_numbers');
    }
}

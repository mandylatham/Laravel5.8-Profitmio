<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UniqueRecipientPhones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unique_recipient_phones', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('unique_recipient_id');
            $table->string('phone', 32)->index('unique_person_phone');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('unique_recipient_phones');
    }
}

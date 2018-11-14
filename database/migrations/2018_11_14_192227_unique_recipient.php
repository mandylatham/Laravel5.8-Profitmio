<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UniqueRecipient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unique_recipient', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('first_name', 191);
            $table->string('last_name', 191);
            $table->string('address', 191);
            $table->string('city', 191);
            $table->string('state', 191);
            $table->string('zip', 191);
            $table->index(['last_name','address','first_name'], 'unique_person_last_first_addr');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('unique_recipient');
    }
}

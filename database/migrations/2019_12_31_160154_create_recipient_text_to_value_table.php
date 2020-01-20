<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecipientTextToValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipient_text_to_value', function (Blueprint $table) {
            $table->increments('id');
            $table->string('text_to_value_code');
            $table->string('text_to_value_amount');
            $table->integer('recipient_id')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipient_text_to_value');
    }
}

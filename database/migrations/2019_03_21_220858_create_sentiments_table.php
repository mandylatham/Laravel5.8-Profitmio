<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentimentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sentiments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('response_id');
            $table->string('sentiment');
            $table->decimal('positive', 18, 17);
            $table->decimal('negative', 18, 17);
            $table->decimal('neutral', 18, 17);
            $table->decimal('mixed', 18, 17);
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
        Schema::dropIfExists('sentiments');
    }
}

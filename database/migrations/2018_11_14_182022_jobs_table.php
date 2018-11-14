<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('queue')->index('idx_queue');
            $table->text('payload');
            $table->tinyInteger('attempts')->unsigned();
            $table->dateTime('reserved_at')->nullable()->index('idx_reserved_at');
            $table->string('available_at', 50)->nullable();
            $table->string('created_at', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}

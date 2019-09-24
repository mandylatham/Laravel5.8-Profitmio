<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecipientActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipient_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('recipient_id');
            $table->string('action', 50);
            $table->dateTime('action_at')->default(now());
            $table->json('metadata')->nullable();
            $table->bigInteger('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipient_activities');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmailLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('campaign_id');
            $table->unsignedInteger('recipient_id');
            $table->string('message_id', 64)->index('message_id');
            $table->string('event', 32);
            $table->string('recipient');
            $table->integer('code');

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
        Schema::dropIfExists('email_logs');
    }
}

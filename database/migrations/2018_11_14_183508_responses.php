<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Responses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('campaign_id');
            $table->unsignedInteger('recipient_id');
            $table->string('type');
            $table->longText('message');
            $table->string('call_sid')->nullable();
            $table->string('recording_sid')->nullable();
            $table->integer('call_phone_number_id')->nullable();
            $table->string('response_source')->nullable();
            $table->string('response_destination')->nullable();
            $table->string('recording_url')->nullable();
            $table->smallInteger('duration');
            $table->string('message_id')->nullable();
            $table->string('in_reply_to')->nullable();
            $table->string('subject')->nullable();
            $table->boolean('incoming')->default(false);
            $table->boolean('read')->default(false);

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
        Schema::dropIfExists('responses');
    }
}

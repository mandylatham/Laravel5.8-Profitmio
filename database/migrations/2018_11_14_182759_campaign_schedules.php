<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CampaignSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('campaign_id')->index('Campaign');
            $table->integer('recipient_group');
            $table->enum('type', ['email','sms','legacy'])->default('legacy');
            $table->string('email_subject')->nullable();
            $table->text('email_html', 65535)->nullable();
            $table->text('email_text', 65535)->nullable();
            $table->text('text_message', 65535)->nullable();
            $table->string('text_message_image')->nullable()->default('');
            $table->boolean('send_vehicle_image')->default(0);
            $table->string('status', 32)->default('Pending');
            $table->smallInteger('percentage_complete')->default(0);
            $table->string('system_id');
            $table->timestamp('send_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('notified_at')->nullable();

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
        Schema::dropIfExists('campaign_schedules');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CampaignScheduleTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_schedule_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->enum('type', ['legacy','email','sms','voice'])->nullable()->default('legacy');
            $table->string('email_subject')->nullable();
            $table->text('email_html', 65535)->nullable();
            $table->text('email_text', 65535)->nullable();
            $table->text('text_message', 65535)->nullable();
            $table->string('text_message_image')->nullable();
            $table->boolean('send_vehicle_image')->default(false);

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
        Schema::dropIfExists('campaign_schedule_templates');
    }
}

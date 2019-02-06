<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('agency_id');
            $table->unsignedInteger('dealership_id');
            $table->unsignedInteger('phone_number_id')->nullable();
            $table->string('name', 255);
            $table->dateTime('expires_at')->nullable();
            $table->integer('order_id');
            $table->boolean('sms_on_callback')->default(false);
            $table->text('sms_on_callback_number')->nullable();
            $table->boolean('service_dept')->default(false);
            $table->text('service_dept_email')->nullable();
            $table->boolean('adf_crm_export')->default(false);
            $table->text('adf_crm_export_email')->nullable();
            $table->boolean('lead_alerts')->default(false);
            $table->text('lead_alert_email')->nullable();
            $table->boolean('client_passthrough')->default(false);
            $table->text('client_passthrough_email')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->enum('status', ['Active', 'Archived', 'Completed', 'Expired', 'Upcoming', 'Cancelled'])->default('Active');

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
        Schema::dropIfExists('campaigns');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipients', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('campaign_id');
            $table->unsignedInteger('recipient_list_id')->nullable();
            $table->unsignedInteger('unique_recipient_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('year')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('vin')->nullable();
            $table->string('carrier')->nullable();
            $table->string('carrier_type')->nullable();
            $table->smallInteger('subgroup')->default(0);
            $table->boolean('service')->default(0);
            $table->boolean('appointment')->default(0);
            $table->boolean('heat')->default(0);
            $table->boolean('interested')->default(0);
            $table->boolean('not_interested')->default(0);
            $table->boolean('wrong_number')->default(0);
            $table->boolean('car_sold')->default(0);
            $table->boolean('callback')->default(false);
            $table->boolean('email_valid')->nullable();
            $table->boolean('phone_valid')->default(false);
            $table->boolean('from_dealer_db')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('last_responded_at')->nullable();

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
        Schema::dropIfExists('recipients');
    }
}

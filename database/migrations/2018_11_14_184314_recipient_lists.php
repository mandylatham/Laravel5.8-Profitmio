<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecipientLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipient_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('campaign_id');
            $table->unsignedInteger('uploaded_by');
            $table->string('name')->nullable();
            $table->string('fieldmap', 2000)->nullable();
            $table->boolean('email_validated')->default(false);
            $table->boolean('recipients_added')->default(false);
            $table->integer('phones_validated')->nullable();
            $table->integer('total_recipients')->nullable();
            $table->integer('total_dealer_db')->nullable();
            $table->integer('total_conquest')->nullable();
            $table->integer('total_valid_phones')->nullable();
            $table->integer('total_valid_emails')->nullable();
            $table->string('upload_identifier', 64)->nullable();
            $table->enum('type', ['database', 'conquest', 'mixed'])->default('conquest');
            $table->string('failed_reason')->nullable();
            $table->timestamp('failed_at')->nullable();

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
        Schema::dropIfExists('recipient_lists');
    }
}

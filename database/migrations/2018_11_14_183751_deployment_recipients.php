<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeploymentRecipients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deployment_recipients', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('deployment_id');
            $table->unsignedInteger('recipient_id');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->unique(['deployment_id','recipient_id'], 'idx_unique_pair');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deployment_recipients');
    }
}

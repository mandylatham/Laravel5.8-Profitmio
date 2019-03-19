<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_verifications', function (Blueprint $table) {
            $table->string('phone', 20);
            $table->string('code', 10);
            $table->integer('attempts')->default(0);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->dateTime('failed_at')->nullable();
            $table->string('failed_reason', 8000)->nullable();
            $table->timestamps();

            $table->primary('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('phone_verifications');
    }
}

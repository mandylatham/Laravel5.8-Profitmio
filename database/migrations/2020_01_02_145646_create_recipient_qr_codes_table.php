<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecipientQrCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipient_qr_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('image_url');
            $table->integer('recipient_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('recipient_id')
                ->references('id')
                ->on('recipients')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipient_qr_codes');
    }
}

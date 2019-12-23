<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_tags', function (Blueprint $table) {
            $table->bigInteger('campaign_id', false, true);
            $table->string('name', 50);
            $table->string('text', 150);
            $table->string('indication');

            $table->primary(['campaign_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lead_tags');
    }
}

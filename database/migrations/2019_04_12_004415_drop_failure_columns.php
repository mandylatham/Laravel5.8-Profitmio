<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropFailureColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_schedules', function (Blueprint $table) {
            $table->timestamp('failed_at')->nullable();
            $table->string('failed_reason')->nullable();
            $table->unsignedInteger('errors_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_schedules', function (Blueprint $table) {
            $table->dropColumn(['failed_at', 'failed_reason', 'errors_count']);
        });
    }
}

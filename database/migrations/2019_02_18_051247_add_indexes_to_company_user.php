<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToCompanyUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_user', function (Blueprint $table) {
            $table->index('user_id', 'user_id_idx');
            $table->index('company_id', 'company_id_idx');
            $table->unique(['user_id', 'company_id'], 'unique_user_company_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_user', function (Blueprint $table) {
            $table->dropIndex('user_id_idx');
            $table->dropIndex('company_id_idx');
            $table->dropIndex('unique_user_company_idx');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->enum('type', ['support', 'agency', 'dealership'])->default('support')->nullable(false);
            $table->string('phone', 50)->nullable();
            $table->string('address', 150)->nullable();
            $table->string('address2', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 80)->nullable();
            $table->string('zip', 10)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('url')->nullable();
            $table->string('image_url')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();

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
        Schema::dropIfExists('companies');
    }
}

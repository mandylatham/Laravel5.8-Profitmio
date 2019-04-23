<?php

use Illuminate\Database\Migrations\Migration;

class AddAppointmentFlagWhereNeeded extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::query()
            ->from('recipients')
            ->whereIn('id', function ($query)
            {
                $query->select(['recipient_id'])
                    ->from('appointments')
                    ->groupBy('recipient_id');
            })
            ->update(['appointment' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

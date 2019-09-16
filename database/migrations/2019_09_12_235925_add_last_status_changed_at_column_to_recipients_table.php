<?php

use App\Models\Lead;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastStatusChangedAtColumnToRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipients', function (Blueprint $table) {
            $table->dateTime('last_status_changed_at')->nullable();
        });

        // Add initial value for this computed column
        Lead::with('responses')->chunk(100, function ($leads) {
            echo "processing next " . count($leads) . " leads:";
            foreach ($leads as $lead) {
                $lsca = $this->calculateInitialValue($lead->responses);
                $lead->update(['last_status_changed_at' => $lsca]);
                echo "updated new row with {$lsca}";
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipients', function (Blueprint $table) {
            $table->dropColumn('last_status_changed_at');
        });
    }

    /**
     * Calculates the initial value for last_status_changed_at
     *
     * @param Collection $responses
     *
     * @return string
     */
    private function calculateInitialValue($responses)
    {
        $lsca = $responses->first()->created_at;

        for ($i=0; $i<count($responses); $i++) {
            if ($i < 1) { continue; }
            if ($responses[$i]->incoming == 1 && $responses[$i-1]->incoming == 0) {
                $lsca = $responses[$i]->created_at;
            }
        }

        return $lsca;
    }
}

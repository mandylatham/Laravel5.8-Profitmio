<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTextToValueTagsSeeder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $inserts = [];
        if (\App\Models\LeadTag::where('name', 'checked-in-from-text-to-value')->count() === 0) {
            $inserts[] = [
                'name' => 'checked-in-from-text-to-value',
                'text' => 'Checked in from text-to-value feature',
                'indication' => 'feature',
                'campaign_id' => 0
            ];
        }
        if (\App\Models\LeadTag::where('name', 'vehicle-value-request-using-text-to-value')->count() === 0) {
            $inserts[] = [
                'name' => 'vehicle-value-requested-using-text-to-value',
                'text' => 'Requested vehicle value using text-to-value feature',
                'indication' => 'feature',
                'campaign_id' => 0
            ];
        }
        \App\Models\LeadTag::insert($inserts);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\LeadTag::where('name', 'checked-in-from-text-to-value')->delete();
        \App\Models\LeadTag::where('name', 'vehicle-value-requested-using-text-to-value')->delete();
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\LeadTag;

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
        if (\App\Models\LeadTag::where('name', LeadTag::CHECKED_IN_FROM_TEXT_TO_VALUE_TAG)->count() === 0) {
            $inserts[] = [
                'name' => LeadTag::CHECKED_IN_FROM_TEXT_TO_VALUE_TAG,
                'text' => 'Checked in from text-to-value feature',
                'indication' => 'feature',
                'campaign_id' => 0
            ];
        }
        if (\App\Models\LeadTag::where('name', LeadTag::VEHICLE_VALUE_REQUESTED_TAG)->count() === 0) {
            $inserts[] = [
                'name' => LeadTag::VEHICLE_VALUE_REQUESTED_TAG,
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
        \App\Models\LeadTag::where('name', LeadTag::CHECKED_IN_FROM_TEXT_TO_VALUE_TAG)->delete();
        \App\Models\LeadTag::where('name', LeadTag::VEHICLE_VALUE_REQUESTED_TAG)->delete();
    }
}

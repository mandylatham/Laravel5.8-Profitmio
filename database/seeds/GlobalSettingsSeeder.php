<?php

use Illuminate\Database\Seeder;
use App\Models\GlobalSettings;

class GlobalSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $globalSettings = new GlobalSettings();
        $globalSettings->name = 'facebook_access_token';
        $globalSettings->save();
    }
}

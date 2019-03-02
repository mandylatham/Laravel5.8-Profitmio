<?php

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Company::class, 1)->create([
            'type' => 'support'
        ]);
        factory(Company::class, 5)->create([
            'type' => 'agency'
        ]);
        factory(Company::class, 5)->create([
            'type' => 'dealership'
        ]);
    }
}

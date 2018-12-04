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
        factory(Company::class, 10)->create([
            'type' => 'support'
        ]);
        factory(Company::class, 10)->create([
            'type' => 'agency'
        ]);
        factory(Company::class, 10)->create([
            'type' => 'dealership'
        ]);
    }
}

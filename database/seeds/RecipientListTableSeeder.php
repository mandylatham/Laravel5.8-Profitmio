<?php

use App\Models\RecipientList;
use Illuminate\Database\Seeder;

class RecipientListTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(RecipientList::class, 10)->create();
    }
}

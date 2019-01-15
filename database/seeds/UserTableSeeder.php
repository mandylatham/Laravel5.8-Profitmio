<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->first_name = 'Cool';
        $user->last_name = 'Developer';
        $user->email = 'admin@example.com';
        $user->username = 'admin';
        $user->is_admin = true;
        $user->password = bcrypt('password');
        $user->save();

        $faker = Faker\Factory::create();
        factory(User::class, 50)
            ->create()
            ->each(function ($user) use ($faker) {
                if ($user->isAdmin()) {
                    $company = Company::where('type', 'support')->first();
                } else {
                    $company = Company::inRandomOrder()->first();
                }
                $company->users()->save($user, [
                    'completed_at' => Carbon::now()->toDateTimeString(),
                    'config' => json_encode([
                        'timezone' => 'US/Alaska'
                    ]),
                    'role' => $faker->randomElement(['admin', 'user']),
                ]);
            });
    }
}

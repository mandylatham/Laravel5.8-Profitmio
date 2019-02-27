<?php

namespace Tests\Browser;

use App\Models\Company;
use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp() : void
    {
        parent::setUp();

        $company = factory(Company::class, 1)->create([
            'type' => 'support'
        ])->first();

        $user = new User();
        $user->first_name = 'Cool';
        $user->last_name = 'Developer';
        $user->email = 'admin@example.com';
        $user->is_admin = true;
        $user->password = bcrypt('password');
        $user->save();

        $company->users()->save($user, [
            'completed_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'config' => json_encode([
                'timezone' => 'US/Alaska'
            ]),
            'role' => 'admin'
        ]);
    }

    public function testRedirectToLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitForRoute('login')
                ->assertSee('Sign into your account');
        });
    }

    public function testWrongUserLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visitRoute('login')
                ->type('email', 'asdf@asdf.com')
                ->type('password', 'password')
                ->click('@login-button')
                ->waitFor('@error-message-2')
                ->assertSee('These credentials do not match our records.');
        });
    }

    public function testSuccessfulLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visitRoute('login')
                ->type('email', 'admin@example.com')
                ->type('password', 'password')
                ->click('@login-button')
                ->waitForRoute('campaigns.index')
                ->assertSee('Campaigns');
        });
    }
}

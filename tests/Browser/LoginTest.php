<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp() : void
    {
        parent::setUp();

        $this->createCompanyAndSiteAdminUser();
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

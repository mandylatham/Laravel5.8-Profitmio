<?php

namespace Tests;

use App\Models\Company;
use App\Models\User;
use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless',
            '--no-sandbox',
            '--window-size=1920,1080',
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    protected function createCompanyAndSiteAdminUser()
    {
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
}

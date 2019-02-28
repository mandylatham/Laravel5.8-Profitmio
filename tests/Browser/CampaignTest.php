<?php

namespace Tests\Browser;

use App\Models\Company;
use App\Models\User;
use Tests\DuskTestCase;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CampaignTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp() : void
    {
        parent::setUp();

        $this->createCompanyAndSiteAdminUser();
    }

    public function testLoadingCampaignIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('campaign.index')
                ->assertSee('Campaigns');
        });
    }

    /**
     * @group test
     */
    public function testSuccessfulCreateCampaign()
    {
        factory(Company::class)->create([
            'type' => 'agency'
        ])->first();
        factory(Company::class)->create([
            'type' => 'dealership'
        ])->first();
        $this->browse(function (Browser $browser) {
            $campaignName = 'Campaitn Test';
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('campaigns.create')
                ->type('name', $campaignName)
                ->type('order', 1)
                ->select('status', 'Active')
                ->datePickSelect('@starts-on-field', Carbon::now()->startOfMonth())
                ->datePickSelect('@ends-on-field', Carbon::now()->addMonth(1)->endOfMonth())
                ->datePickSelect('@expires-on-field', Carbon::now()->addMonth(2)->endOfMonth())
                ->click('.wizard-footer-right .wizard-btn')
                ->vueSelect('@dealership-select', 0)
                ->vueSelect('@agency-select', 0)
                ->click('.wizard-footer-right .wizard-btn')
                ->click('.wizard-footer-right .wizard-btn')
                ->check('adf_crm_export')
                ->with('.adf_crm_export-container', function ($container) {
                    $email1 = 'user1@pf.com';
                    $email2 = 'user2@pf.com';
                    $container->type('.form-control', $email1)
                        ->click('.btn')
                        ->type('.form-control', $email2)
                        ->click('.btn')
                        ->pause(300)
                        ->assertSee($email1)
                        ->assertSee($email2);
                })
                ->click('.wizard-footer-right .wizard-btn')
                ->waitFor('.swal2-container', 10)
                ->assertSee('Campaign Created!')
                ->click('button.swal2-confirm')
                ->waitForRoute('campaigns.index')
                ->waitFor('.campaign-group-label')
                ->assertSee($campaignName);
        });
    }
}

<?php

namespace Tests\Browser;

use App\Models\Campaign;
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

    public function testUserCanGoToCreateCampaignFromCampaignIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('campaigns.index')
                ->click('@create-campaign-button')
                ->waitForRoute('campaigns.create')
                ->assertRouteIs('campaigns.create');
        });
    }

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

    public function testSetOnAllAdditionalFeaturesOnEditCampaignPage()
    {
        // Create Campaign
        $agency = factory(Company::class)->create([
            'type' => 'agency'
        ])->first();
        $dealership = factory(Company::class)->create([
            'type' => 'dealership'
        ])->first();
        $campaign = factory(Campaign::class)->create([
            'agency_id' => $agency->id,
            'dealership_id' => $dealership->id,
            'status' => 'Active',
            'starts_at' => Carbon::now()->startOfMonth()->toDateString(),
            'ends_at' => Carbon::now()->addMonth(1)->toDateString(),
            'expires_at' => Carbon::now()->addMonth(2)->toDateString()
        ])->first();
        $this->browse(function (Browser $browser) use ($campaign) {
            $browser
                ->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('campaigns.edit', ['campaign' => $campaign->id])
                ->click('.card-header-tabs .nav-item:last-child');
            foreach (['adf_crm_export', 'lead_alerts', 'service_dept', 'client_passthrough'] as $field) {
                $browser->with('.' . $field . '-container', function ($container) use ($field) {
                    $container
                        ->check('input[type="checkbox"]')
                        ->type('.form-control', $field.'1@pf.com')
                        ->click('.btn')
                        ->type('.form-control', $field.'2@pf.com')
                        ->click('.btn')
                        ->pause(200)
                        ->assertSee($field.'1@pf.com')
                        ->assertSee($field.'2@pf.com');
                });
            }
            $browser->with('.sms_on_callback-container', function ($container) {
                $phone1 = '202-555-0154';
                $phone2 = '202-555-0155';
                $container
                    ->check('input[type="checkbox"]')
                    ->type('.form-control', $phone1)
                    ->click('.btn')
                    ->type('.form-control', $phone2)
                    ->click('.btn')
                    ->pause(200)
                    ->assertSee($phone1)
                    ->assertSee($phone2);
            });
            // Save campaign and assert if fields were saved correctly
            $browser
                ->click('@save-additional-features-button')
                ->waitFor('.swal2-container', 10)
                ->assertSee('Campaign Updated!')
                ->refresh()
                ->click('.card-header-tabs .nav-item:last-child');

            foreach (['adf_crm_export', 'lead_alerts', 'service_dept', 'client_passthrough'] as $field) {
                $browser->assertSee($field.'1@pf.com')
                    ->assertSee($field.'2@pf.com');
            }

            $browser->assertSee('202-555-0154')
                ->assertSee('202-555-0155');
        });
    }
}

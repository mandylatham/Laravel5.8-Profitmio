<?php

namespace Tests\Browser;

use App\Models\Campaign;
use App\Models\Company;
use App\Models\User;
use Tests\DuskTestCase;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DropTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $campaign;

    protected $recipientList;

    public function setUp() : void
    {
        parent::setUp();

        $this->createCompanyAndSiteAdminUser();

        // Create Campaign
        $agency = factory(Company::class)->create([
            'type' => 'agency'
        ])->first();
        $dealership = factory(Company::class)->create([
            'type' => 'dealership'
        ])->first();
        $this->campaign = factory(Campaign::class)->create([
            'agency_id' => $agency->id,
            'dealership_id' => $dealership->id,
            'status' => 'Active',
            'starts_at' => Carbon::now()->startOfMonth()->toDateString(),
            'ends_at' => Carbon::now()->addMonth(1)->toDateString(),
            'expires_at' => Carbon::now()->addMonth(2)->toDateString()
        ])->first();

        // Create recipient list
        $this->recipientList = factory(\App\Models\RecipientList::class)
            ->create([
                'name' => 'Recipient List Demo',
                'campaign_id' => $this->campaign->id
            ])
            ->first();
        // Attach recipients to recipient list
        factory(\App\Models\Recipient::class, 100)
            ->create([
                'campaign_id' => $this->campaign->id,
                'recipient_list_id' => $this->recipientList->id
            ]);
    }

    public function testUserCanGoToDropsIndexPageFromCampaignIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('campaigns.index', ['campaign' => $this->campaign->id])
                ->waitUntilMissing('.loader-spinner')
                ->click('#campaign-component-' . $this->campaign->id . ' .drop-link')
                ->waitForRoute('campaigns.drops.index', ['campaign' => $this->campaign->id])
                ->assertRouteIs('campaigns.drops.index', ['campaign' => $this->campaign->id]);
        });
    }

    public function testUserCanGoToCreateDropFromDropIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('campaigns.drops.index', ['campaign' => $this->campaign->id])
                ->waitUntilMissing('.loader-spinner')
                ->click('@new-drop-button')
                ->waitForRoute('campaigns.drops.create', ['campaign' => $this->campaign->id])
                ->assertRouteIs('campaigns.drops.create', ['campaign' => $this->campaign->id]);
        });
    }

    public function testSuccessfulCreateSmsDropAllSmsAbleWithoutTemplateFromConquests()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('campaigns.drops.create', ['campaign' => $this->campaign->id])
                ->waitUntilMissing('.table-loader-spinner', 20)
                ->vueSelect('@contact-method-select', 0)
                ->waitUntilMissing('.table-loader-spinner', 20)
                ->click('@data-source-database-check')
                ->waitUntilMissing('.table-loader-spinner', 20)
                ->type('@max-per-group-field', 2)
                ->waitUntilMissing('.table-loader-spinner', 20)
                ->append('@max-per-group-field', 0)
                ->waitUntilMissing('.table-loader-spinner', 20)
                ->click('.wizard-footer-right .wizard-btn')
                ->select('@drop-type-select', 'Sms')
                ->type('@drop-text-message-input', 'Message')
                ->click('.wizard-footer-right .wizard-btn')
                ->assertElementsCounts('.schedule-time-table tbody tr.time-row', 5);
            for ($i = 0, $date = Carbon::now(); $i < 5; $i++, $date->addDays(5)) {
                $browser->pause(300)->selectDateTime('@group-datetime-'.$i, $date, '.time-row td:first-child');
            }
            $browser->click('.wizard-footer-right .wizard-btn')
                ->waitFor('.swal2-container', 10)
                ->assertSee('Drop Created!')
                ->click('button.swal2-confirm')
                ->waitForRoute('campaigns.drops.index', ['campaign' => $this->campaign->id])
                ->waitUntilMissing('.table-loader-spinner', 10)
                ->assertElementsCounts('.drop', 5);
        });
    }

    public function testSuccessfulCreateEmailDropAllEmailAbleWithoutTemplateFromConquests()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('campaigns.drops.create', ['campaign' => $this->campaign->id])
                ->waitUntilMissing('.table-loader-spinner', 20)
                ->vueSelect('@contact-method-select', 1)
                ->waitUntilMissing('.table-loader-spinner', 20)
                ->click('@data-source-database-check')
                ->waitUntilMissing('.table-loader-spinner', 20)
                ->type('@max-per-group-field', 2)
                ->waitUntilMissing('.table-loader-spinner', 20)
                ->append('@max-per-group-field', 0)
                ->waitUntilMissing('.table-loader-spinner', 20)
                ->click('.wizard-footer-right .wizard-btn')
                ->select('@drop-type-select', 'email')
                ->type('@drop-email-subject-input', 'Email Subject')
                ->type('@drop-email-text-input', 'Email Text')
                ->type('.drop-email-html .ace_text-input', '<p>Email html template</p>')
                ->click('.wizard-footer-right .wizard-btn')
                ->assertElementsCounts('.schedule-time-table tbody tr.time-row', 5);
            for ($i = 0, $date = Carbon::now(); $i < 5; $i++, $date->addDays(5)) {
                $browser->selectDateTime('@group-datetime-'.$i, $date, '.time-row td:first-child');
            }
            $browser->click('.wizard-footer-right .wizard-btn')
                ->waitFor('.swal2-container', 10)
                ->assertSee('Drop Created!')
                ->click('button.swal2-confirm')
                ->waitForRoute('campaigns.drops.index', ['campaign' => $this->campaign->id])
                ->waitUntilMissing('.table-loader-spinner', 10)
                ->assertElementsCounts('.drop', 5);
        });
    }
}

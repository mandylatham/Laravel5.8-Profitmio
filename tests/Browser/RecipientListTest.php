<?php

namespace Tests\Browser;

use App\Models\Campaign;
use App\Models\Company;
use App\Models\User;
use Tests\DuskTestCase;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RecipientListTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $campaign;

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
    }

    public function testLoadingRecipientListIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('campaigns.recipient-lists.index', ['campaign' => $this->campaign->id])
                ->assertRouteIs('campaigns.recipient-lists.index', ['campaign' => $this->campaign->id]);
        });
    }

    public function testUserCanGoToRecipientListPageFromCampaignIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('campaigns.index', ['campaign' => $this->campaign->id])
                ->waitUntilMissing('.loader-spinner')
                ->click('#campaign-component-' . $this->campaign->id . ' .recipient-list-link')
                ->waitForRoute('campaigns.recipient-lists.index', ['campaign' => $this->campaign->id])
                ->assertRouteIs('campaigns.recipient-lists.index', ['campaign' => $this->campaign->id]);
        });
    }

    public function testSuccessfulCreateRecipientList()
    {
        $listName = 'Recipient List Test';
        $recipientListFields = [
            'first_name',
            'last_name',
            'email',
            'phone',
            'address1',
            'state',
            'city',
            'zip',
            'year',
            'make',
            'model',
            'vin'
        ];
        $this->browse(function (Browser $browser) use ($listName, $recipientListFields) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('campaigns.recipient-lists.index', ['campaign' => $this->campaign->id])
                ->click('@add-recipient-list-button')
                ->waitFor('.upload-recipient-component')
                ->with('.upload-recipient-component', function ($element) use ($listName, $recipientListFields) {
                    $element->attach('input[type="file"]', base_path('tests/Browser/recipient_list_test.csv'))
                        ->waitFor('.wizard-nav li:nth-child(2).active')
                        ->type('pm_list_name', $listName)
                        ->click('.wizard-footer-right .wizard-btn')
                        ->waitFor('.wizard-nav li:nth-child(3).active');
                    // Verify fieldmap
                    foreach ($recipientListFields as $field) {
                        $element->assertVue('fileForm.uploaded_file_fieldmap.' . $field, $field, '');
                    }
                })
                ->click('.wizard-footer-right .wizard-btn')
                ->waitFor('.swal2-container', 10)
                ->assertSee('Recipients Uploaded!')
                ->click('button.swal2-confirm')
                ->waitForRoute('campaigns.recipient-lists.index', ['campaign' => $this->campaign->id])
                ->waitUntilMissing('.table-loader-spinner')
                ->assertSee($listName);
        });
    }
}

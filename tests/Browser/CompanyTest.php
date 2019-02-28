<?php

namespace Tests\Browser;

use App\Models\Company;
use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CompanyTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp() : void
    {
        parent::setUp();

        $this->createCompanyAndSiteAdminUser();
    }

    public function testCompanyIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('company.index')
                ->assertSee('Companies');
        });
    }

    public function testCreateCompanyButton()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('company.index')
                ->click('@create-company-button')
                ->assertRouteIs('company.create');
        });
    }

    public function testSuccessfulCreateDealershipCompany()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('company.create')
                ->type('name', 'Dealership Company')
                ->select('type', 'dealership')
                ->click('.wizard-footer-right .wizard-btn')
                ->select('country', 'us')
                ->type('phone', '+1-541-754-3010')
                ->type('address', '450 NW. Whitemarsh Street San Pablo')
                ->type('city', 'California')
                ->type('state', 'Los Angeles')
                ->type('zip', '20001')
                ->click('.wizard-footer-right .wizard-btn')
                ->click('.wizard-footer-right .wizard-btn')
                ->waitFor('.swal2-container')
                ->assertSee('Company Added!');
        });
    }

    public function testSuccessfulCreateAgencyCompany()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('company.create')
                ->type('name', 'Dealership Company')
                ->select('type', 'dealership')
                ->click('.wizard-footer-right .wizard-btn')
                ->select('country', 'us')
                ->type('phone', '+1-541-754-3010')
                ->type('address', '450 NW. Whitemarsh Street San Pablo')
                ->type('city', 'California')
                ->type('state', 'Los Angeles')
                ->type('zip', '20001')
                ->click('.wizard-footer-right .wizard-btn')
                ->click('.wizard-footer-right .wizard-btn')
                ->waitFor('.swal2-container')
                ->assertSee('Company Added!');
        });
    }

    public function testEditAgencyCompany()
    {
        $company = factory(Company::class)->create([
            'type' => 'agency'
        ])->first();
        $newAddress = '216 Spring St. King Of Prussia';
        $this->browse(function (Browser $browser) use ($company, $newAddress) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('company.details', ['company' => $company->id])
                ->click('@edit-button')
                ->type('address', $newAddress)
                ->click('@save-company-button')
                ->waitFor('.toast.toast-success')
                ->assertSee('Update successful');
        });
    }

    public function testEditDealershipCompany()
    {
        $company = factory(Company::class)->create([
            'type' => 'dealership'
        ])->first();
        $newAddress = '216 Spring St. King Of Prussia';
        $this->browse(function (Browser $browser) use ($company, $newAddress) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('company.details', ['company' => $company->id])
                ->click('@edit-button')
                ->type('address', $newAddress)
                ->click('@save-company-button')
                ->waitFor('.toast.toast-success')
                ->assertSee('Update successful');
        });
    }
}

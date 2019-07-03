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

    public function testLoadingCompanyIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('company.index')
                ->assertSee('Companies');
        });
    }

    public function testUserCanGoToCreateCompanyPageFromCompanyIndexPage()
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
                ->attach('input[type="file"]', resource_path('img/logo.png'))
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
                ->attach('input[type="file"]', resource_path('img/logo.png'))
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

    public function testEditAddressAgencyCompany()
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

    public function testEditAddressDealershipCompany()
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

    public function testCreateCompanyAdminForDealershipCompanyFromCompanyDetailsPage() {
        $company = factory(Company::class)->create([
            'type' => 'dealership'
        ])->first();
        $this->browse(function (Browser $browser) use ($company) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('company.details', ['company' => $company->id])
                ->click('.card-header-tabs .nav-item:nth-child(2)')
                ->click('@add-user-button')
                ->waitForRoute('user.create')
                ->vueSelect('@role-select', 0)
                ->type('email', 'ca@dealership.com')
                ->vueSelect('@company-select', 0)
                ->click('@save-user-button')
                ->waitFor('.swal2-container', 10)
                ->assertSee('Invitation Sent')
                ->click('button.swal2-confirm')
                ->visitRoute('user.index')
                ->assertSee('Users');
        });
    }

    public function testCreateCompanyUserForDealershipCompanyFromCompanyDetailsPage() {
        $company = factory(Company::class)->create([
            'type' => 'dealership'
        ])->first();
        $this->browse(function (Browser $browser) use ($company) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('company.details', ['company' => $company->id])
                ->click('.card-header-tabs .nav-item:nth-child(2)')
                ->click('@add-user-button')
                ->waitForRoute('user.create')
                ->vueSelect('@role-select', 1)
                ->type('email', 'cu@dealership.com')
                ->vueSelect('@company-select', 0)
                ->click('@save-user-button')
                ->screenshot('1')
                ->waitFor('.swal2-container', 20)
                ->assertSee('Invitation Sent')
                ->click('button.swal2-confirm')
                ->visitRoute('user.index')
                ->assertSee('Users');
        });
    }

    public function testCreateCompanyAdminForAgencyCompanyFromCompanyDetailsPage() {
        $company = factory(Company::class)->create([
            'type' => 'agency'
        ])->first();
        $this->browse(function (Browser $browser) use ($company) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('company.details', ['company' => $company->id])
                ->click('.card-header-tabs .nav-item:nth-child(2)')
                ->click('@add-user-button')
                ->waitForRoute('user.create')
                ->vueSelect('@role-select', 0)
                ->type('email', 'ca@dealership.com')
                ->vueSelect('@company-select', 0)
                ->click('@save-user-button')
                ->waitFor('.swal2-container', 10)
                ->assertSee('Invitation Sent')
                ->click('button.swal2-confirm')
                ->visitRoute('user.index')
                ->assertSee('Users');
        });
    }

    public function testCreateCompanyUserForAgencyCompanyFromCompanyDetailsPage() {
        $company = factory(Company::class)->create([
            'type' => 'agency'
        ])->first();
        $this->browse(function (Browser $browser) use ($company) {
            $browser->loginAs(User::where('is_admin', 1)->first())
                ->visitRoute('company.details', ['company' => $company->id])
                ->click('.card-header-tabs .nav-item:nth-child(2)')
                ->click('@add-user-button')
                ->waitForRoute('user.create')
                ->vueSelect('@role-select', 1)
                ->type('email', 'cu@dealership.com')
                ->vueSelect('@company-select', 0)
                ->click('@save-user-button')
                ->waitFor('.swal2-container', 10)
                ->assertSee('Invitation Sent')
                ->click('button.swal2-confirm')
                ->visitRoute('user.index')
                ->assertSee('Users');
        });
    }
}

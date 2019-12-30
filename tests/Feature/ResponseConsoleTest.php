<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Lead;
use App\Models\Recipient;
use App\Models\LeadActivity;
use App\Events\CampaignCountsUpdated;
use App\Jobs\CalculateCampaignUserScore;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResponseConsoleTest extends TestCase
{
    use RefreshDatabase;

    public $agency = null;
    public $campaign = null;
    public $dealership = null;
    public $user = null;
    public $recipient = null;

    public function setUp() : void
    {
        parent::setUp();

        session()->flush();
        // Setup Companies
        $this->agency = factory('App\Models\Company')->create(['type' => 'agency']);
        $this->assertDatabaseHas('companies', $this->agency->only('id'));

        $this->dealership = factory('App\Models\Company')->create(['type' => 'dealership']);
        $this->assertDatabaseHas('companies', $this->dealership->only('id'));

        // Setup Campaign
        $this->campaign = factory('App\Models\Campaign')->create([
            'agency_id' => $this->agency->id,
            'dealership_id' => $this->dealership->id,
        ]);
        $this->assertDatabaseHas('campaigns', $this->campaign->only('id'));

        foreach ($this->campaign->responses as $response) {
            $this->assertEquals($this->campaign->id, $response->campaign_id);
        }

        // Setup User
        $this->user = factory('App\Models\User')->create(['is_admin' => false]);
        $this->assertDatabaseHas('users', $this->user->only('id'));

        // Attach User to Dealership
        $this->user->companies()->attach($this->dealership->id, ['role' => 'user']);
        $this->assertDatabaseHas('company_user', [
            'user_id' => $this->user->id,
            'company_id' => $this->dealership->id,
            'role' => 'user',
        ]);
        session()->put('activeCompany', $this->dealership->id);

        // Attach User to Campaign
        $this->user->campaigns()->attach($this->campaign->id);
        $this->assertDatabaseHas('campaign_user', [
            'user_id' => $this->user->id,
            'campaign_id' => $this->campaign->id,
        ]);

        // Set Lead
        $this->recipient = factory('App\Models\Recipient')->create([
            'campaign_id' => $this->campaign->id,
            'last_name' => 'test',
            'status' => Lead::NEW_STATUS,
            'last_status_changed_at' => now(),
        ]);
        $this->assertDatabaseHas('recipients', [
            'id' => $this->recipient->id,
            'status' => Recipient::NEW_STATUS,
        ]);

        // Setup Response
        $response = factory('App\Models\Response')->create([
            'campaign_id' => $this->campaign->id,
            'recipient_id' => $this->recipient->id,
            'type' => 'text',
            'message' => 'Please tell me more',
            'created_at' => now()->subHour(),
        ]);
        $this->assertDatabaseHas('responses', ['id' => $response->id]);
    }

    /** @test */
    public function a_user_can_view_the_dashboard()
    {
        $this->actingAs($this->user)
             ->get('/')
             ->assertRedirect('/dashboard/');
    }

    /** @test */
    public function a_user_can_view_the_console()
    {
        $this->actingAs($this->user)
             ->get('/campaign/'.$this->campaign->id.'/response-console')
             ->assertStatus(200);
    }

    /** @test */
    public function a_user_can_retrieve_console_leads()
    {
        $url = route('lead.index', ['campaign' => $this->campaign->id]);

        $this->actingAs($this->user)
             ->get($url)
             ->assertStatus(200);
    }

    /** @test */
    public function a_user_can_see_lead_details()
    {
        $this->withoutExceptionHandling();

        $url = route('campaign.recipient.responses', ['campaign' => $this->campaign->id, 'lead' => $this->recipient->id]);

        $this->actingAs($this->user)
             ->get($url)
             ->assertStatus(200);
    }

    /** @test */
    public function a_user_can_search_console_leads_by_name()
    {
        $url = route('lead.index', ['campaign' => $this->campaign->id]);

        $this->actingAs($this->user)
             ->get($url, ['search' => 'test'])
             ->assertSee('test');
    }

    /** @test */
    public function a_user_can_open_a_new_lead()
    {
        $this->withoutExceptionHandling();

        $url = route('lead.open', ['lead' => $this->recipient->id]);

        $this->actingAs($this->user)
             ->post($url)
             ->assertStatus(200);
    }
}

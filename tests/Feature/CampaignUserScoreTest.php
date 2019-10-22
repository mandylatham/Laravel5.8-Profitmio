<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CampaignUserScoreTest extends TestCase
{
    /** @test */
    public function a_user_gets_full_points_for_opening_lead_in_time()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function a_user_gets_partial_points_for_opening_a_lead_late()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function a_user_gets_minimum_points_for_opening_a_lead_too_late()
    {
        $this->assertTrue(true);
    }
}

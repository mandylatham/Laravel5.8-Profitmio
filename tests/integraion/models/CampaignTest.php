<?php

use App\Models\Campaign;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CampaignTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    function it_fetches_active_campaigns()
    {
        // Given
        factory(Campaign::class, 3)->create();
        factory(Campaign::class, 3)->create(['starts_at' => now()->subYear(1), 'ends_at' => now()->subMonths(3), 'expires_at' => now()->subMonths(1)]);

        // When
        // Then
    }
}
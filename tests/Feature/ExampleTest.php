<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // public function testUsersEndpointResponseTime()
    // {
    //     $start = microtime(true);

    //     // Simulate GET request to your API route
    //     $response = $this->getJson('/api/loan/ledger/summary');

    //     $time = microtime(true) - $start;

    //     // Check response status
    //     $response->assertStatus(200);

    //     // Fail if API takes more than 200ms
    //     $this->assertLessThan(0.2, $time, "API /api/users took too long");
    // }
}

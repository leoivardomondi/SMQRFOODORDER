<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_unauthenticated_api_requests_receive_json_response()
    {
        $response = $this->getJson('/api/login');

        $response->assertStatus(401)->assertJson(['errors' => 'unauthenticated']);
    }
}

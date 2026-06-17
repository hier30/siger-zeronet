<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_redirects_to_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('dashboard'));
    }

    public function test_login_page_returns_a_successful_response(): void
    {
        $this->withoutVite();

        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}

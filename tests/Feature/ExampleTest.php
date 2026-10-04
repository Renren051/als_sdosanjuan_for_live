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

        $response->assertStatus(200)
            ->assertSee('SDO San Juan City')
            ->assertSee('Alternative Learning System')
            ->assertSee('A &amp; E Program', false)
            ->assertSee('Enrolment Requirements')
            ->assertSee('San Juan Learning Centers')
            ->assertSee('Contact SDO San Juan City ALS');
    }
}

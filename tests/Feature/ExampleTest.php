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
            ->assertSee('Alternative Learning System');

        $this->get('/about-als')->assertStatus(200)->assertSee('About Alternative Learning System');
        $this->get('/programs')->assertStatus(200)->assertSee('Accreditation &amp; Equivalency', false);
        $this->get('/requirements')->assertStatus(200)->assertSee('Enrolment Requirements');
        $this->get('/learning-centers')->assertStatus(200)->assertSee('San Juan Learning Centers');
        $this->get('/contact')->assertStatus(200)->assertSee('Contact SDO San Juan City ALS');
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    public function test_chat_endpoint_responds_successfully_with_alsjay(): void
    {
        $response = $this->postJson('/chat', [
            'message' => 'who are you?',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $reply = $response->json('reply');
        $this->assertStringContainsString('ALSJay', $reply);
    }

    public function test_tagalog_query_returns_tagalog_alsjay_response(): void
    {
        $response = $this->postJson('/chat', [
            'message' => 'sino ka po?',
        ]);

        $response->assertStatus(200);
        $reply = $response->json('reply');
        $this->assertStringContainsString('ALSJay', $reply);
        $this->assertTrue(str_contains($reply, 'po') || str_contains($reply, 'Ako'));
    }

    public function test_coding_query_is_refused_by_guardrails(): void
    {
        $response = $this->postJson('/chat', [
            'message' => 'write a python script for a calculator',
        ]);

        $response->assertStatus(200);
        $reply = $response->json('reply');
        $this->assertStringContainsString('ALSJay', $reply);
        $this->assertStringContainsString('cannot assist with', $reply);
    }

    public function test_homework_solving_query_is_refused_by_guardrails(): void
    {
        $response = $this->postJson('/chat', [
            'message' => 'sagutan mo assignment ko sa math',
        ]);

        $response->assertStatus(200);
        $reply = $response->json('reply');
        $this->assertStringContainsString('ALSJay', $reply);
        $this->assertStringContainsString('Hindi po ako', $reply);
    }

    public function test_requirements_fallback_returns_official_deped_documents(): void
    {
        config(['services.gemini.api_key' => null]);

        $response = $this->postJson('/chat', [
            'message' => 'ano ang mga requirements sa pag-enroll?',
        ]);

        $response->assertStatus(200);
        $reply = $response->json('reply');
        $this->assertStringContainsString('1x1', $reply);
        $this->assertStringContainsString('PSA Birth Certificate', $reply);
        $this->assertStringContainsString('Brgy. Certificate of Residency', $reply);
        $this->assertStringContainsString('1 Valid ID', $reply);
        $this->assertStringContainsString('DepEd.gov.ph', $reply);
    }

    public function test_qualifications_fallback_returns_age_and_eligibility(): void
    {
        config(['services.gemini.api_key' => null]);

        $response = $this->postJson('/chat', [
            'message' => 'sinu-sino ang mga qualified kumuha ng test?',
        ]);

        $response->assertStatus(200);
        $reply = $response->json('reply');
        $this->assertStringContainsString('Elementary Level', $reply);
        $this->assertStringContainsString('Secondary Level', $reply);
        $this->assertStringContainsString('DepEd.gov.ph', $reply);
    }
}

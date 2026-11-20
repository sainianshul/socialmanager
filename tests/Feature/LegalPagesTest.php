<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    public function test_privacy_policy_page_renders(): void
    {
        $response = $this->get('/privacy');
        $response->assertStatus(200);
        $response->assertSee('Privacy Policy');
    }

    public function test_terms_of_service_page_renders(): void
    {
        $response = $this->get('/terms');
        $response->assertStatus(200);
        $response->assertSee('Terms of Service');
    }

    public function test_data_deletion_page_renders(): void
    {
        $response = $this->get('/data-deletion');
        $response->assertStatus(200);
        $response->assertSee('User Data Deletion Instructions');
    }

    public function test_data_deletion_post_callback(): void
    {
        $response = $this->post('/data-deletion');
        $response->assertStatus(200);
        $response->assertJsonStructure(['url', 'confirmation_code']);
    }
}

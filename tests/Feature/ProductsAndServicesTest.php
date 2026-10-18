<?php

namespace Tests\Feature;

use App\Models\RedirectRule;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsAndServicesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed core data recovery services
        Service::create([
            'title' => 'Hard Drive Data Recovery Dubai',
            'slug' => 'hard-drive-data-recovery-dubai',
            'tagline' => 'Certified ISO Class 100 Cleanroom Lab • 98.7% Proven Success Rate',
            'summary' => 'Comprehensive data recovery for clicking, failing, dropped, corrupted, or unreadable desktop and laptop hard drives.',
            'status' => 'published',
            'order' => 1,
            'features' => [
                'Mechanical Head Assembly Replacement',
                'Platter Surface Cleanroom Inspection',
                'Firmware Corruption & Service Area Repair',
            ],
            'benefits' => [
                '100% "No Data, No Charge" Guarantee',
                'Free In-Lab Diagnostic Report',
            ],
        ]);

        Service::create([
            'title' => 'RAID Data Recovery Dubai',
            'slug' => 'raid-data-recovery-service-dubai',
            'tagline' => 'Enterprise Multi-Disk RAID Array Reconstruction',
            'summary' => 'Mission-critical data recovery for failed RAID arrays and SAN/NAS storage.',
            'status' => 'published',
            'order' => 2,
        ]);

        // Seed 301 redirects for legacy generator URLs
        foreach (['/rental-power', '/power-plant', '/amc-generator', '/new-generators'] as $genUrl) {
            RedirectRule::create([
                'source_url' => $genUrl,
                'target_url' => '/services',
                'status_code' => 301,
                'is_active' => true,
            ]);
        }
    }

    public function test_home_page_displays_data_recovery_services_and_no_generators(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Specialized Data Recovery Services');
        $response->assertSee('Hard Drive Data Recovery Dubai');
        $response->assertSee('RAID Data Recovery Dubai');

        // Confirm ZERO generator mentions on homepage
        $response->assertDontSee('Diesel Generator Rental');
        $response->assertDontSee('Multi-Megawatt Power Plants');
        $response->assertDontSee('kVA');
        $response->assertDontSee('Portacabin');
    }

    public function test_services_index_displays_data_recovery_services(): void
    {
        $response = $this->get('/services');

        $response->assertStatus(200);
        $response->assertSee('Professional Data Recovery');
        $response->assertSee('Hard Drive Data Recovery Dubai');
        $response->assertSee('RAID Data Recovery Dubai');

        $response->assertDontSee('Turnkey Diesel Generator');
        $response->assertDontSee('Multi-Megawatt Power Plants');
    }

    public function test_service_detail_page_displays_cleanroom_details_and_features(): void
    {
        $response = $this->get('/service/hard-drive-data-recovery-dubai');

        $response->assertStatus(200);
        $response->assertSee('Hard Drive Data Recovery Dubai');
        $response->assertSee('ISO-5 CLASS 100 CLEANROOM');
        $response->assertSee('Mechanical Head Assembly Replacement');
        $response->assertSee('No Data, No Charge');
    }

    public function test_legacy_generator_urls_redirect_to_services(): void
    {
        foreach (['/rental-power', '/power-plant', '/amc-generator', '/new-generators'] as $genUrl) {
            $response = $this->get($genUrl);
            $response->assertStatus(301);
            $response->assertRedirect('/services');
        }
    }

    public function test_contact_form_submission_for_data_recovery_succeeds(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Tariq Al Mansoori',
            'email' => 'tariq@example.ae',
            'phone' => '+971 50 123 4567',
            'emirate' => 'Bur Dubai Main Lab',
            'device_type' => 'Hard Drive',
            'subject' => 'Clicking Hard Drive Recovery',
            'message' => 'Dropped external 2TB WD hard drive making clicking sound.',
        ]);

        $response->assertSessionHas('success');
    }
}

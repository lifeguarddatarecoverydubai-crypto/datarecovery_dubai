<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Inquiry;
use App\Models\Page;
use App\Models\Post;
use App\Models\RedirectRule;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\DataRecoveryServicesContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecoveryDataSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create([
            'name' => 'Recovery Specialist',
            'email' => 'admin@recoverydata.ae',
            'role' => 'admin',
        ]);

        Service::create([
            'title' => 'Hard Drive Data Recovery',
            'slug' => 'hard-drive-recovery',
            'tagline' => 'Class 100 cleanroom recovery',
            'summary' => 'Comprehensive recovery for clicking and dead drives.',
            'status' => 'published',
            'order' => 1,
        ]);

        $category = Category::create([
            'name' => 'Blogs',
            'slug' => 'blogs',
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'SSD Showing Wrong Capacity Recovery Dubai',
            'slug' => 'ssd-showing-wrong-capacity-recovery-dubai',
            'excerpt' => 'How to recover data from SSD showing 0GB.',
            'content' => '<p>Detailed SSD recovery guide in Dubai.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $post->categories()->attach($category->id);

        Page::create([
            'user_id' => $user->id,
            'title' => 'Professional Data Recovery Dubai',
            'slug' => 'data-recovery-dubai',
            'content' => '<p>Professional data recovery lab in Dubai.</p>',
            'status' => 'published',
            'template' => 'default',
        ]);
    }

    public function test_home_page_renders_with_recovery_data_branding(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('LifeGuard Data Recovery');
        $response->assertSee('Data Recovery');
        $response->assertDontSee('Diesel Generator');
    }

    public function test_about_page_renders_with_cleanroom_details(): void
    {
        $redirectResponse = $this->get('/about');
        $redirectResponse->assertStatus(301);
        $redirectResponse->assertRedirect('/about-us');

        $response = $this->get('/about-us');
        $response->assertStatus(200);
        $response->assertSee('LifeGuard Data Recovery');
    }

    public function test_blog_index_and_post_detail_render_successfully(): void
    {
        $response = $this->get('/blog');
        $response->assertStatus(200);
        $response->assertSee('SSD Showing Wrong Capacity Recovery Dubai');

        $postResponse = $this->get('/ssd-showing-wrong-capacity-recovery-dubai');
        $postResponse->assertStatus(200);
        $postResponse->assertSee('SSD Showing Wrong Capacity Recovery Dubai');
        $postResponse->assertSee('Detailed SSD recovery guide in Dubai.');

        // Legacy /post/ URL 301 redirects to root permalink
        $legacyRedirect = $this->get('/post/ssd-showing-wrong-capacity-recovery-dubai');
        $legacyRedirect->assertStatus(301);
        $legacyRedirect->assertRedirect('/ssd-showing-wrong-capacity-recovery-dubai');
    }

    public function test_wordpress_direct_slug_resolution(): void
    {
        // 1. Post via direct slug renders canonical 200 (preserving Google Page 1 rankings)
        $postSlugResponse = $this->get('/ssd-showing-wrong-capacity-recovery-dubai');
        $postSlugResponse->assertStatus(200);
        $postSlugResponse->assertSee('SSD Showing Wrong Capacity Recovery Dubai');

        // 2. Page via direct slug renders canonical 200
        $pageSlugResponse = $this->get('/data-recovery-dubai');
        $pageSlugResponse->assertStatus(200);
        $pageSlugResponse->assertSee('Professional Data Recovery Dubai');
    }

    public function test_contact_inquiry_submission_stores_to_database(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Ahmed Al Nuaimi',
            'email' => 'ahmed@company.ae',
            'phone' => '+971 50 999 8888',
            'subject' => 'Mechanical Hard Drive (Clicking noises)',
            'message' => 'My external WD 4TB hard drive fell and is making clicking sounds.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('inquiries', [
            'email' => 'ahmed@company.ae',
            'name' => 'Ahmed Al Nuaimi',
        ]);
    }

    public function test_login_page_renders_with_recovery_data_branding(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('images/logo.png');
        $response->assertSee('CMS to manage operations');
    }

    public function test_admin_dashboard_does_not_contain_odoo_or_products(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertDontSee('Odoo Partner Page');
        $response->assertDontSee('Edit Odoo Partner Page');
        $response->assertDontSee('Add Product');
        $response->assertSee('Services');
        $response->assertSee('Leads & Inquiries', false);
    }

    public function test_all_nine_data_recovery_services_render_with_rich_content(): void
    {
        $this->seed(DataRecoveryServicesContentSeeder::class);

        $services = [
            'hard-drive-recovery' => 'Hard Drive Data Recovery Dubai',
            'ssd-recovery-dubai' => 'SSD Data Recovery Dubai',
            'nvme-ssd-recovery-dubai' => 'NVMe SSD Recovery Dubai',
            'raid-recovery-dubai' => 'RAID & Server Data Recovery Dubai',
            'nas-recovery-dubai' => 'NAS Data Recovery Dubai',
            'cctv-footage-recovery-dubai' => 'CCTV Footage Recovery Dubai',
            'usb-data-recovery-dubai' => 'USB & Pen Drive Recovery Dubai',
            'iphone-data-recovery-dubai' => 'iPhone & Mobile Phone Recovery Dubai',
            'ransomware-data-recovery-dubai' => 'Ransomware Data Recovery Dubai',
        ];

        foreach ($services as $slug => $title) {
            $response = $this->get('/service/'.$slug);
            $response->assertStatus(200);
            $response->assertSee($title);
            if ($slug === 'hard-drive-recovery') {
                $response->assertSee('ISO Class 100');
            }
            $response->assertSee('+971 43 933 082');
            $response->assertSee('Frequently Asked Questions');
        }

        // Test services index page
        $indexResponse = $this->get('/services');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Professional Data Recovery', false);
        $indexResponse->assertSee('Services Dubai');

        foreach ($services as $slug => $title) {
            $indexResponse->assertSee($title);
        }
    }

    public function test_admin_dashboard_displays_all_services_and_pages_for_direct_editing(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Services (');
        $response->assertSee('Hard Drive Data Recovery');

        $response->assertSee('Edit Page');
        $response->assertSee('Website Pages');
        $response->assertSee('Professional Data Recovery Dubai');
    }

    public function test_admin_can_edit_all_service_fields_and_preserves_rich_html(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $service = Service::where('slug', 'hard-drive-recovery')->first();

        // Check edit view loads with all fields
        $editResponse = $this->actingAs($admin)->get(route('admin.services.edit', $service));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Cleanroom Lab Standards & SLA Benchmarks', false);
        $editResponse->assertSee('Detailed Service Content');

        $editResponse->assertSee('HTML Source Code');
        $editResponse->assertSee('Core Technical Capabilities');

        // Update with full data including custom hero badge, benchmarks, and custom HTML table
        $customHtml = '<div class="custom-card"><h3 class="text-xl">Cleanroom Protocol</h3><table class="w-full"><tr><td>Status</td><td>Verified</td></tr></table></div>';

        $updateResponse = $this->actingAs($admin)->put(route('admin.services.update', $service), [
            'title' => 'Updated HDD Cleanroom Service',
            'slug' => 'hard-drive-recovery',
            'tagline' => 'Updated 24/7 Forensic Recovery',
            'hero_badge' => 'Certified Class 100 Lab Dubai #508',
            'benchmark_standard' => 'ISO Class 100 / FED STD 209E Custom',
            'benchmark_evaluation' => '100% Free 2-Hour Diagnostic',
            'benchmark_guarantee' => 'No Data No Charge Policy',
            'benchmark_turnaround' => '4-Hour Emergency or 24h Standard',
            'benchmark_courier' => 'Free Express Courier Across UAE',
            'summary' => 'Updated summary for hard disk recovery.',
            'content' => $customHtml,
            'features_input' => "HDA Head Swap\nPlatter Transplant\nFirmware Repair",
            'benefits_input' => "Zero Risk Guarantee\nNDA Confidentiality",
            'icon' => 'hard-drive',
            'status' => 'published',
            'order' => 2,
            'is_featured' => 1,
            'meta_title' => 'Updated SEO Title',
            'meta_description' => 'Updated meta description.',
        ]);

        $updateResponse->assertRedirect(route('admin.services.edit', $service));

        $service->refresh();
        $this->assertEquals('Updated HDD Cleanroom Service', $service->title);
        $this->assertEquals('Certified Class 100 Lab Dubai #508', $service->hero_badge);
        $this->assertEquals('ISO Class 100 / FED STD 209E Custom', $service->benchmarks['standard']);
        $this->assertEquals($customHtml, $service->content);
        $this->assertCount(3, $service->features);
        $this->assertCount(2, $service->benefits);

        // Verify frontend displays the updated fields
        $showResponse = $this->get('/service/hard-drive-recovery');
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Updated HDD Cleanroom Service');
        $showResponse->assertSee('HDA Head Swap');
        $showResponse->assertSee('Zero Risk Guarantee');
        $showResponse->assertSee('Cleanroom Protocol');
    }

    public function test_root_slug_route_prioritizes_published_service_over_legacy_page(): void
    {
        // When user visits /hard-drive-recovery root URL, it renders the published service
        $response = $this->get('/hard-drive-recovery');
        $response->assertStatus(200);
        $response->assertSee('Hard Drive Data Recovery');
    }

    public function test_intake_form_creates_evaluation_inquiry_with_device_model_and_pickup(): void
    {
        $response = $this->post(route('contact.submit'), [
            'name' => 'Rashid Al Maktoum',
            'email' => 'rashid@enterprise.ae',
            'phone' => '+971501234567',
            'subject' => 'Evaluation Request: Hard Drive Data Recovery',
            'device_model' => 'Western Digital 4TB My Passport',
            'pickup_requested' => 'yes',
            'message' => 'Clicking sound after dropping on floor.',
        ]);

        $response->assertSessionHas('success');
        $response->assertSessionMissing('errors');

        $this->assertDatabaseHas('inquiries', [
            'name' => 'Rashid Al Maktoum',
            'email' => 'rashid@enterprise.ae',
            'type' => 'evaluation',
        ]);

        $inquiry = Inquiry::where('email', 'rashid@enterprise.ae')->first();
        $this->assertStringContainsString('Western Digital 4TB My Passport', $inquiry->message);
        $this->assertStringContainsString('Free Courier Pickup Requested', $inquiry->message);
        $this->assertStringContainsString('Clicking sound', $inquiry->message);
    }

    public function test_dynamic_sitemap_xml_returns_valid_xml_with_all_services_and_posts(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $content = $response->getContent();

        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
        $this->assertStringContainsString('xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"', $content);
        $this->assertStringContainsString('/hard-drive-recovery', $content);
        $this->assertStringContainsString('/ssd-showing-wrong-capacity-recovery-dubai', $content);
    }

    public function test_cannibalizing_and_legacy_urls_return_301_redirects_to_canonical_targets(): void
    {
        RedirectRule::seedDefaults();

        // 1. Legacy duplicate page to service
        $resp1 = $this->get('/page/hard-drive-recovery');

        $resp1->assertStatus(301);
        $resp1->assertRedirect(route('service.show', 'hard-drive-recovery'));

        // 2. Legacy direct slug to service
        $resp2 = $this->get('/hard-drive-data-recovery-in-dubai');
        $resp2->assertStatus(301);
        $resp2->assertRedirect(route('service.show', 'hard-drive-recovery'));

        // 3. Server recovery synonym to RAID service
        $resp3 = $this->get('/server-recovery-dubai');
        $resp3->assertStatus(301);
        $resp3->assertRedirect(route('service.show', 'raid-recovery-dubai'));

        // 4. SD card synonym to USB service
        $resp4 = $this->get('/sd-card-recovery-dubai');
        $resp4->assertStatus(301);
        $resp4->assertRedirect(route('service.show', 'usb-data-recovery-dubai'));

        // 5. Post slug conflict 301 redirect
        $resp5 = $this->get('/post/ssd-recovery-dubai');
        $resp5->assertStatus(301);
        $resp5->assertRedirect(route('service.show', 'ssd-recovery-dubai'));

        // 6. Generic CMS pages to clean routes
        $this->get('/page/about')->assertStatus(301)->assertRedirect(route('about'));
        $this->get('/page/contact')->assertStatus(301)->assertRedirect(route('contact'));
        $this->get('/page/services')->assertStatus(301)->assertRedirect(route('services.index'));
        $this->get('/page/home')->assertStatus(301)->assertRedirect(route('home'));
    }

    public function test_service_page_includes_faqpage_schema_and_related_case_studies(): void
    {
        $service = Service::where('slug', 'hard-drive-recovery')->first();
        $service->update([
            'content' => '<h3>Can data be recovered from a clicking hard drive?</h3><p>Yes, inside our ISO Class 100 cleanroom laboratory.</p>',
        ]);

        $response = $this->get('/service/hard-drive-recovery');

        $response->assertStatus(200);
        $response->assertSee('"@type": "FAQPage"', false);
        $response->assertSee('"@type": "BreadcrumbList"', false);
        $response->assertSee('"@type": "Service"', false);
        $response->assertSee('Related Case Studies & Technical Guides', false);

    }

    public function test_blog_post_includes_contextual_commercial_service_banner_and_blogposting_schema(): void
    {
        $post = Post::published()->first();
        $this->assertNotNull($post);

        $response = $this->get('/'.$post->slug);

        $response->assertStatus(200);
        $response->assertSee('"@type": "BlogPosting"', false);
        $response->assertSee('"@type": "BreadcrumbList"', false);
        $response->assertSee('EMERGENCY LAB TRIAGE');
        $response->assertSee('ISO-5 Class 100 Cleanroom');
    }
}

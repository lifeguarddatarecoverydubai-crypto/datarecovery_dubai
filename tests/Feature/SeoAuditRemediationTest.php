<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\RedirectRule;
use App\Models\Service;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoAuditRemediationTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_date_archive_urls_redirect_to_blog(): void
    {
        $response = $this->get('/2024/04/01/');
        $response->assertStatus(301);
        $response->assertRedirect(route('blog.index'));

        $response2 = $this->get('/2025/08/');
        $response2->assertStatus(301);
        $response2->assertRedirect(route('blog.index'));
    }

    public function test_legacy_dated_post_urls_redirect_to_root_slug(): void
    {
        $response = $this->get('/2024/04/01/recovering-formatted-nvme-ssd');
        $response->assertStatus(301);
        $response->assertRedirect('/recovering-formatted-nvme-ssd');
    }

    public function test_legacy_wp_content_broken_images_redirect_to_fallback(): void
    {
        $response = $this->get('/wp-content/uploads/2026/09/lifeguarddatarecovery-1-23.jpg');
        $response->assertStatus(301);
        $response->assertRedirect('/images/logo.webp');
    }

    public function test_screaming_frog_301_redirect_rules_are_honored(): void
    {
        RedirectRule::create([
            'source_url' => '/hard-drive-data-recovery/',
            'target_url' => '/hard-drive-data-recovery-dubai/',
            'status_code' => 301,
            'is_active' => true,
        ]);

        RedirectRule::create([
            'source_url' => '/server-repair-services/',
            'target_url' => '/server-data-recovery-dubai/',
            'status_code' => 301,
            'is_active' => true,
        ]);

        $res1 = $this->get('/hard-drive-data-recovery/');
        $res1->assertStatus(301);
        $res1->assertRedirect('/hard-drive-data-recovery-dubai/');

        $res2 = $this->get('/server-repair-services/');
        $res2->assertStatus(301);
        $res2->assertRedirect('/server-data-recovery-dubai/');
    }

    public function test_post_title_has_no_duplicate_brand_suffix_and_proper_title_case(): void
    {
        $user = User::factory()->create();

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'how to recover important file from a non working ssd - LifeGuard Data Recovery',
            'slug' => 'how-to-recover-important-file-from-a-non-working-ssd',
            'content' => '<p>Recovery steps</p>',
            'status' => 'published',
            'published_at' => now(),
            'meta_title' => 'how to recover important file from a non working ssd - LifeGuard Data Recovery',
        ]);

        $response = $this->get('/how-to-recover-important-file-from-a-non-working-ssd');
        $response->assertStatus(200);

        // Verify title has no duplicate brand suffix
        $response->assertDontSee('LifeGuard Data Recovery - LifeGuard Data Recovery');
        $response->assertDontSee('LifeGuard Data Recovery Dubai - LifeGuard Data Recovery');
        $response->assertSee('<title>How to Recover Important File from a Non Working SSD - LifeGuard Data Recovery</title>', false);
    }

    public function test_services_index_is_canonical_and_all_services_301_redirects(): void
    {
        // /all-services must 301 redirect to /services
        $redirectResponse = $this->get('/all-services');
        $redirectResponse->assertStatus(301);
        $redirectResponse->assertRedirect('/services');

        // /services must be 200 OK and canonicalize to /services
        $indexResponse = $this->get('/services');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('<link rel="canonical" href="'.route('services.index').'">', false);
    }

    public function test_blog_index_renders_category_filter_pills_and_popular_tags(): void
    {
        $user = User::factory()->create();
        $cat = Category::create([
            'name' => 'SSD Forensics',
            'slug' => 'ssd-forensics',
        ]);
        $tag = Tag::create([
            'name' => 'NVMe Repair',
            'slug' => 'nvme-repair',
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'SSD Firmware Recovery Guide',
            'slug' => 'ssd-firmware-recovery-guide',
            'content' => '<p>SSD guide</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $post->categories()->attach($cat->id);
        $post->tags()->attach($tag->id);

        $response = $this->get('/blog');
        $response->assertStatus(200);
        $response->assertSee('Filter by Topic:');
        $response->assertSee(route('category.show', 'ssd-forensics'));
        $response->assertSee('SSD Forensics');
    }

    public function test_website_and_local_business_schema_rendered_site_wide(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // LocalBusiness / ProfessionalService schema
        $response->assertSee('"@type": "ProfessionalService"', false);

        // WebSite schema with SearchAction
        $response->assertSee('"@type": "WebSite"', false);
        $response->assertSee('"@type": "SearchAction"', false);
    }

    public function test_has_featured_image_trait_normalizes_paths_across_all_models(): void
    {
        $user = User::factory()->create();

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Test Image Path Post',
            'slug' => 'test-image-path-post',
            'featured_image' => '/storage/storage/media/2026/09/sample.webp',
            'status' => 'published',
        ]);

        $page = Page::create([
            'title' => 'Test Image Path Page',
            'slug' => 'test-image-path-page',
            'featured_image' => 'storage/media/page.webp',
            'status' => 'published',
            'template' => 'default',
        ]);

        $service = Service::create([
            'title' => 'Test Image Path Service',
            'slug' => 'test-image-path-service',
            'featured_image' => '/media/service.webp',
            'status' => 'published',
        ]);

        $this->assertEquals('/storage/media/2026/09/sample.webp', $post->featured_image_url);
        $this->assertEquals('/storage/media/page.webp', $page->featured_image_url);
        $this->assertEquals('/storage/media/service.webp', $service->featured_image_url);
    }

    public function test_location_pages_and_services_have_two_way_internal_linking(): void
    {
        Page::create([
            'title' => 'Data Recovery Dubai Cleanroom Lab',
            'slug' => 'data-recovery-dubai',
            'content' => '<p>Lab details in Bur Dubai</p>',
            'status' => 'published',
            'template' => 'default',
        ]);

        $service = Service::create([
            'title' => 'Hard Drive Data Recovery Dubai',
            'slug' => 'hard-drive-data-recovery-dubai',
            'summary' => 'Cleanroom hard drive diagnostics',
            'status' => 'published',
        ]);

        // Location page links to services and branch hubs
        $pageResponse = $this->get('/data-recovery-dubai');
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Specialized Data Recovery Services at this Location');
        $pageResponse->assertSee('/hard-drive-data-recovery-dubai');
        $pageResponse->assertSee('/data-recovery-al-barsha');

        // Service page links to UAE location hubs
        $serviceResponse = $this->get('/service/hard-drive-data-recovery-dubai');
        $serviceResponse->assertStatus(200);
        $serviceResponse->assertSee('Available Across All UAE Emirates', false);
        $serviceResponse->assertSee('/data-recovery-dubai');
        $serviceResponse->assertSee('/data-recovery-in-business-bay');
    }

    public function test_sitemaps_contain_zero_broken_storage_storage_image_urls(): void
    {
        $post = Post::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Sitemap Test Article',
            'slug' => 'sitemap-test-article',
            'content' => '<p>Body</p>',
            'featured_image' => '/storage/storage/media/posts/sitemap-test.webp',
            'status' => 'published',
            'published_at' => now(),
            'is_indexable' => true,
        ]);

        $response = $this->get('/sitemap-posts.xml');
        $response->assertStatus(200);

        $xml = $response->getContent();
        $this->assertStringNotContainsString('/storage/storage', $xml);
        $this->assertStringContainsString('/storage/media/posts/sitemap-test.webp', $xml);
    }
}

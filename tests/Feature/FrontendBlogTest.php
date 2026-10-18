<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendBlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_successfully_and_shows_published_posts(): void
    {
        $user = User::factory()->create();

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Sample Public Article',
            'slug' => 'sample-public-article',
            'content' => '<p>Article content</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sample Public Article');
        $response->assertSee('<meta name="viewport" content="width=device-width, initial-scale=1">', false);
    }

    public function test_single_post_renders_with_seo_meta_tags(): void
    {
        $user = User::factory()->create();

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Article with SEO',
            'slug' => 'article-with-seo',
            'content' => '<p>Rich article content</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'meta_title' => 'Targeted SEO Title for Crawlers',
            'meta_description' => 'Targeted meta description for Google index.',
            'meta_keywords' => 'keyword1, keyword2',
            'is_indexable' => true,
        ]);

        $response = $this->get('/article-with-seo');
        $response->assertStatus(200);
        $response->assertSee('<title>Targeted SEO Title for Crawlers', false);
        $response->assertSee('content="Targeted meta description for Google index."', false);
        $response->assertSee('content="keyword1, keyword2"', false);

        // Legacy /post/ URL 301-redirects to root permalink
        $this->get('/post/article-with-seo')->assertStatus(301)->assertRedirect('/article-with-seo');
    }

    public function test_static_page_renders_with_seo_meta(): void
    {
        $user = User::factory()->create();

        $page = Page::create([
            'user_id' => $user->id,
            'title' => 'Company Mission',
            'slug' => 'company-mission',
            'content' => '<p>Our company values and vision.</p>',
            'status' => 'published',
            'meta_title' => 'Company Mission Statement',
            'meta_description' => 'Learn about our core principles.',
        ]);

        $response = $this->get('/page/company-mission');
        $response->assertStatus(200);
        $response->assertSee('Company Mission Statement');
        $response->assertSee('Our company values and vision.', false);
    }

    public function test_draft_post_returns_404_for_guest(): void
    {
        $user = User::factory()->create();

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Draft Post Title',
            'slug' => 'draft-post-title',
            'content' => '<p>Secret content</p>',
            'status' => 'draft',
        ]);

        $response = $this->get('/draft-post-title');
        $response->assertStatus(404);
    }

    public function test_author_name_is_not_displayed_on_blog_pages(): void
    {
        $user = User::factory()->create([
            'name' => 'Shafia Begum',
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Comprehensive Data Recovery Strategy',
            'slug' => 'comprehensive-data-recovery-strategy',
            'content' => '<p>Data recovery guide details.</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        // Home page
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Comprehensive Data Recovery Strategy');
        $homeResponse->assertDontSee('Shafia Begum');

        // Blog listing
        $blogResponse = $this->get('/blog');
        $blogResponse->assertStatus(200);
        $blogResponse->assertSee('Comprehensive Data Recovery Strategy');
        $blogResponse->assertDontSee('Shafia Begum');

        // Single post
        $showResponse = $this->get('/comprehensive-data-recovery-strategy');
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Comprehensive Data Recovery Strategy');
        $showResponse->assertDontSee('Shafia Begum');
        $showResponse->assertDontSee('Written by');

        // Legacy /post/ URL 301-redirects to root permalink
        $this->get('/post/comprehensive-data-recovery-strategy')->assertStatus(301)->assertRedirect('/comprehensive-data-recovery-strategy');

        // Numeric author ID 301-redirects to slug-based URL (Item 12)
        $authorResponse = $this->get('/author/'.$user->id);
        $authorResponse->assertStatus(301);
        $authorResponse->assertRedirect(route('author.show', $user->slug));

        // Slug-based author URL renders successfully
        $slugResponse = $this->get('/author/'.$user->slug);
        $slugResponse->assertStatus(200);
        $slugResponse->assertSee('Shafia Begum');
        $slugResponse->assertSee('Comprehensive Data Recovery Strategy');
    }

    public function test_single_post_renders_with_service_right_sidebar_including_form_hotline_and_services(): void
    {
        $user = User::factory()->create();

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Recovering Formatted NVMe SSD and Flash Storage',
            'slug' => 'recovering-formatted-nvme-ssd-and-flash-storage',
            'content' => '<p>Deep technical article detailing cleanroom flash memory engineering.</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $service = Service::create([
            'title' => 'Hard Drive Data Recovery Dubai',
            'slug' => 'hard-drive-data-recovery-dubai',
            'summary' => 'ISO Class 100 cleanroom mechanical drive recovery.',
            'status' => 'published',
            'order' => 1,
        ]);

        $response = $this->get('/recovering-formatted-nvme-ssd-and-flash-storage');
        $response->assertStatus(200);

        // Right sidebar lab diagnostic form
        $response->assertSee('Free Diagnostic Quote');
        $response->assertSee('ISO-5 Cleanroom Lab Evaluation');
        $response->assertSee('name="phone"', false);
        $response->assertSee('name="emirate"', false);
        $response->assertSee('Book Free Lab Evaluation');

        // Right sidebar 24/7 hotline and WhatsApp card
        $response->assertSee('24/7 Critical Data Hotline');
        $response->assertSee('+971 43 933 082');
        $response->assertSee('Chat With Senior Lab Engineer');
        $response->assertSee('All 7 Emirates');

        // Right sidebar service navigation
        $response->assertSee('Recovery Services');
        $response->assertSee('Hard Drive Data Recovery Dubai');
        $response->assertSee(route('service.show', 'hard-drive-data-recovery-dubai'));
    }

    public function test_blog_sidebar_evaluation_form_submits_successfully(): void
    {
        $response = $this->post(route('contact.submit'), [
            'name' => 'Khalid Al Nuaimi',
            'phone' => '+971 50 999 8888',
            'email' => 'khalid@example.ae',
            'device_model' => 'WD My Passport 4TB',
            'pickup_requested' => 'yes',
            'message' => 'Hard drive dropped from table, makes clicking sound.',
            'subject' => 'Evaluation Request from Blog: NVMe SSD Controller Failure Repair',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('inquiries', [
            'name' => 'Khalid Al Nuaimi',
            'email' => 'khalid@example.ae',
            'phone' => '+971 50 999 8888',
            'subject' => 'Evaluation Request from Blog: NVMe SSD Controller Failure Repair',
            'type' => 'evaluation',
        ]);
    }

    public function test_pages_render_valid_json_ld_schema_with_unescaped_context(): void
    {
        $user = User::factory()->create();

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Valid Schema Test Post',
            'slug' => 'valid-schema-test-post',
            'content' => '<p>Post content with schema</p>',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $urls = ['/', route('post.show', $post->slug), route('about'), route('contact')];

        foreach ($urls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);

            $html = $response->getContent();

            // Ensure no Blade context directive artifacts leak into HTML
            $this->assertStringNotContainsString('__contextArgs', $html);
            $this->assertStringNotContainsString('context()->has', $html);

            // Extract all JSON-LD scripts
            preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches);
            $this->assertNotEmpty($matches[1], "No JSON-LD found on {$url}");

            foreach ($matches[1] as $rawJson) {
                $decoded = json_decode(trim($rawJson), true);
                $this->assertNotNull($decoded, "Failed to decode JSON-LD on {$url}");
                $this->assertArrayHasKey('@context', $decoded, "Missing @context property on {$url}");
                $this->assertEquals('https://schema.org', $decoded['@context']);
            }
        }
    }
}

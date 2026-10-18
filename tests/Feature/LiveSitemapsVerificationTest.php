<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveSitemapsVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test sitemap structure definitions.
     */
    public function test_sitemap_counts_match_spec(): void
    {
        $sitemapsFile = base_path('scratch/live_sitemaps.json');
        if (! file_exists($sitemapsFile)) {
            $this->markTestSkipped('live_sitemaps.json not found');
        }

        $data = json_decode(file_get_contents($sitemapsFile), true);

        // 1. Verify 397 posts from post-sitemap.xml
        $this->assertCount(397, $data['posts']);

        // 2. Verify 88 pages from page-sitemap.xml
        $this->assertCount(88, $data['pages']);

        // 3. Verify 7 categories from category-sitemap.xml
        $this->assertCount(7, $data['categories']);
    }

    /**
     * Test categories render with proper headings and posts.
     */
    public function test_categories_render_with_headings_and_posts(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'name' => 'Data Recovery',
            'slug' => 'data-recovery',
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Recovering Formatted NVMe SSD',
            'slug' => 'recovering-formatted-nvme-ssd',
            'content' => '<h2>Symptoms of Formatted NVMe Drives</h2><p>Article body content.</p>',
            'status' => 'published',
            'published_at' => now(),
            'is_indexable' => true,
        ]);

        $post->categories()->attach($category->id);

        $response = $this->get('/category/data-recovery');
        $response->assertStatus(200);
        $response->assertSee('Data Recovery');
        $response->assertSee('Recovering Formatted NVMe SSD');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use App\Services\WebpConverterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WebpConversionTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'two_factor_confirmed_at' => now(),
        ]);
    }

    public function test_webp_converter_service_detects_convertible_mimes(): void
    {
        $service = new WebpConverterService;

        $this->assertTrue($service->isConvertible('image/jpeg', 'jpg'));
        $this->assertTrue($service->isConvertible('image/png', 'png'));
        $this->assertTrue($service->isConvertible('image/gif', 'gif'));
        $this->assertTrue($service->isConvertible('image/webp', 'webp'));
        $this->assertFalse($service->isConvertible('image/svg+xml', 'svg'));
        $this->assertFalse($service->isConvertible('application/pdf', 'pdf'));
    }

    public function test_uploading_jpeg_automatically_converts_to_webp(): void
    {
        $file = UploadedFile::fake()->image('test-banner.jpg', 800, 600);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['two_factor_authenticated' => true])
            ->post(route('admin.media.store'), [
                'file' => $file,
            ]);

        $this->assertDatabaseHas('media', [
            'mime_type' => 'image/webp',
        ]);

        $media = Media::where('name', 'test-banner')->latest()->first();
        $this->assertNotNull($media);
        $this->assertStringEndsWith('.webp', $media->file_name);
        $this->assertStringEndsWith('.webp', $media->file_path);
        $this->assertEquals('image/webp', $media->mime_type);
        $this->assertNotNull($media->dimensions);
    }

    public function test_uploading_png_automatically_converts_to_webp(): void
    {
        $file = UploadedFile::fake()->image('logo.png', 400, 200);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['two_factor_authenticated' => true])
            ->post(route('admin.media.store'), [
                'file' => $file,
            ]);

        $media = Media::where('name', 'logo')->latest()->first();
        $this->assertNotNull($media);
        $this->assertStringEndsWith('.webp', $media->file_name);
        $this->assertEquals('image/webp', $media->mime_type);
    }

    public function test_uploading_image_on_post_automatically_converts_to_webp(): void
    {
        $imageFile = UploadedFile::fake()->image('hard-drive-cleanroom.png', 1200, 800);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['two_factor_authenticated' => true])
            ->post(route('admin.posts.store'), [
                'title' => 'Cleanroom Hard Drive Diagnostics',
                'slug' => 'cleanroom-hard-drive-diagnostics',
                'content' => '<p>Deep cleanroom recovery details.</p>',
                'status' => 'published',
                'featured_image_file' => $imageFile,
            ]);

        $post = Post::where('slug', 'cleanroom-hard-drive-diagnostics')->first();
        $this->assertNotNull($post);
        $this->assertStringEndsWith('.webp', $post->featured_image);
        $this->assertStringStartsWith('/storage/', $post->featured_image);
    }

    public function test_uploading_image_on_page_automatically_converts_to_webp(): void
    {
        $imageFile = UploadedFile::fake()->image('lab-facility.jpg', 1000, 600);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['two_factor_authenticated' => true])
            ->post(route('admin.pages.store'), [
                'title' => 'Our Dubai Cleanroom Lab',
                'slug' => 'our-dubai-cleanroom-lab',
                'content' => '<p>State of the art recovery lab.</p>',
                'template' => 'default',
                'status' => 'published',
                'featured_image_file' => $imageFile,
            ]);

        $page = Page::where('slug', 'our-dubai-cleanroom-lab')->first();
        $this->assertNotNull($page);
        $this->assertStringEndsWith('.webp', $page->featured_image);
        $this->assertStringStartsWith('/storage/', $page->featured_image);
    }

    public function test_uploading_image_on_service_automatically_converts_to_webp(): void
    {
        $imageFile = UploadedFile::fake()->image('server-rack.png', 1400, 900);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['two_factor_authenticated' => true])
            ->post(route('admin.services.store'), [
                'title' => 'SAN Storage Data Recovery',
                'slug' => 'san-storage-data-recovery',
                'summary' => 'SAN and Enterprise storage recovery.',
                'content' => '<p>Enterprise SAN recovery procedures.</p>',
                'status' => 'published',
                'featured_image_file' => $imageFile,
            ]);

        $service = Service::where('slug', 'san-storage-data-recovery')->first();
        $this->assertNotNull($service);
        $this->assertStringEndsWith('.webp', $service->featured_image);
        $this->assertStringStartsWith('/storage/', $service->featured_image);
    }

    public function test_all_admin_dashboard_pages_render_successfully(): void
    {
        $routes = [
            route('admin.dashboard'),
            route('admin.posts.index'),
            route('admin.posts.create'),
            route('admin.pages.index'),
            route('admin.pages.create'),
            route('admin.services.index'),
            route('admin.services.create'),
            route('admin.categories.index'),
            route('admin.tags.index'),
            route('admin.media.index'),
            route('admin.menus.index'),
            route('admin.redirects.index'),
            route('admin.inquiries.index'),
            route('admin.users.index'),
            route('admin.profile.edit'),
            route('admin.odoo.edit'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($this->adminUser)
                ->withSession(['two_factor_authenticated' => true])
                ->get($url);

            $this->assertEquals(200, $response->getStatusCode(), "Failed rendering admin dashboard route: $url");
        }
    }
}

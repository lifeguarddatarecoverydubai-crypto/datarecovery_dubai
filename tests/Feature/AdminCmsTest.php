<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_editor_can_view_admin_dashboard(): void
    {
        $editor = User::factory()->create([
            'role' => 'editor',
            'is_active' => true,
        ]);

        $response = $this->actingAs($editor)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Admin Panel');
    }

    public function test_editor_can_create_post_with_seo_meta(): void
    {
        $editor = User::factory()->create([
            'role' => 'editor',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Technology',
            'slug' => 'technology',
        ]);

        $response = $this->actingAs($editor)->post(route('admin.posts.store'), [
            'title' => 'My First Great Post',
            'slug' => 'my-first-great-post',
            'excerpt' => 'A wonderful excerpt of the post.',
            'content' => '<p>Article body formatted nicely.</p>',
            'status' => 'published',
            'categories' => [$category->id],
            'tags_input' => 'laravel, testing',
            'meta_title' => 'Custom SEO Title for Google',
            'meta_description' => 'Custom meta description for search engine ranking.',
            'meta_keywords' => 'laravel, testing, cms',
            'is_indexable' => 1,
        ]);

        $post = Post::where('slug', 'my-first-great-post')->first();
        $this->assertNotNull($post);
        $this->assertEquals('Custom SEO Title for Google', $post->meta_title);
        $this->assertEquals('Custom meta description for search engine ranking.', $post->meta_description);
        $this->assertTrue($post->categories->contains($category));
        $this->assertCount(2, $post->tags);

        $response->assertRedirect(route('admin.posts.edit', $post));
    }

    public function test_admin_can_create_new_user(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New Contributor',
            'email' => 'contributor@example.com',
            'password' => 'Password123!',
            'role' => 'editor',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'contributor@example.com',
            'role' => 'editor',
        ]);
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $editor = User::factory()->create([
            'role' => 'editor',
            'is_active' => true,
        ]);

        $response = $this->actingAs($editor)->get(route('admin.users.index'));
        $response->assertStatus(403);
    }

    public function test_deleting_user_does_not_delete_posts_and_reassigns_to_admin(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $editor = User::factory()->create([
            'role' => 'editor',
            'name' => 'Editor To Delete',
            'email' => 'editor.delete@example.com',
            'is_active' => true,
        ]);

        $post = Post::create([
            'user_id' => $editor->id,
            'title' => 'Important Article',
            'slug' => 'important-article',
            'content' => 'Article content that must never be deleted',
            'status' => 'published',
        ]);

        $page = Page::create([
            'user_id' => $editor->id,
            'title' => 'Important Page',
            'slug' => 'important-page',
            'content' => 'Page content',
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $editor));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $editor->id]);

        // Assert post still exists and was safely preserved and reassigned to admin
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'user_id' => $admin->id,
            'title' => 'Important Article',
        ]);

        // Assert page still exists and was safely preserved and reassigned to admin
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'user_id' => $admin->id,
            'title' => 'Important Page',
        ]);
    }

    public function test_editor_can_view_post_edit_screen_and_rich_content_is_present(): void
    {
        $editor = User::factory()->create([
            'role' => 'editor',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Cleanroom Data Recovery',
            'slug' => 'cleanroom-data-recovery',
        ]);

        $post = Post::create([
            'user_id' => $editor->id,
            'title' => 'Recovering Clicking Mechanical Hard Drives in Cleanroom',
            'slug' => 'recovering-clicking-mechanical-hard-drives-cleanroom',
            'excerpt' => 'Deploying cleanroom micro-soldering and head transplants.',
            'content' => '<div class="space-y-8"><div class="p-6 bg-amber-500/10"><h3>Engineering Standards</h3><p>Critical benchmarks for UAE sites.</p></div></div>',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $post->categories()->attach($category);

        $response = $this->actingAs($editor)->get(route('admin.posts.edit', $post));
        $response->assertStatus(200);

        // Assert content is rendered in both the hidden textarea and the visual/code editors
        $response->assertSee('Engineering Standards');
        $response->assertSee('Critical benchmarks for UAE sites.');
        $response->assertSee('name="content"', false);
        $response->assertSee('id="postContentInput"', false);
        $response->assertSee('id="visualEditorContainer"', false);
        $response->assertSee('id="postCodeTextarea"', false);
        $response->assertSee('Cleanroom Data Recovery');
    }

    public function test_editor_can_update_post_content_and_rich_html_is_preserved(): void
    {
        $editor = User::factory()->create([
            'role' => 'editor',
            'is_active' => true,
        ]);

        $post = Post::create([
            'user_id' => $editor->id,
            'title' => 'Original Post Title',
            'slug' => 'original-post-title',
            'excerpt' => 'Original excerpt.',
            'content' => '<p>Original content.</p>',
            'status' => 'draft',
        ]);

        $updatedContent = '<div class="space-y-6"><h2>Updated Technical Standard</h2><p>Full 100% preserved rich HTML without tag stripping.</p></div>';

        $response = $this->actingAs($editor)->put(route('admin.posts.update', $post), [
            'title' => 'Updated Post Title',
            'slug' => 'updated-post-title',
            'excerpt' => 'Updated excerpt text.',
            'content' => $updatedContent,
            'status' => 'published',
            'published_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('admin.posts.edit', $post));

        $post->refresh();
        $this->assertEquals('Updated Post Title', $post->title);
        $this->assertEquals('updated-post-title', $post->slug);
        $this->assertEquals($updatedContent, $post->content);
        $this->assertEquals('published', $post->status);
    }

    public function test_deleting_user_reassigns_content_to_specified_admin(): void
    {
        $currentAdmin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $targetAdmin = User::factory()->create([
            'name' => 'Target Editor',
            'role' => 'editor',
            'is_active' => true,
        ]);

        $author = User::factory()->create([
            'name' => 'Departing Author',
            'role' => 'author',
            'is_active' => true,
        ]);

        $post1 = Post::create([
            'user_id' => $author->id,
            'title' => 'Author Post 1',
            'slug' => 'author-post-1',
            'excerpt' => 'Excerpt 1',
            'content' => '<p>Post content 1</p>',
            'status' => 'published',
        ]);

        $post2 = Post::create([
            'user_id' => $author->id,
            'title' => 'Author Post 2',
            'slug' => 'author-post-2',
            'excerpt' => 'Excerpt 2',
            'content' => '<p>Post content 2</p>',
            'status' => 'draft',
        ]);

        $page = Page::create([
            'user_id' => $author->id,
            'title' => 'Author Page',
            'slug' => 'author-page',
            'content' => '<p>Page content</p>',
            'status' => 'published',
        ]);

        $response = $this->actingAs($currentAdmin)->delete(route('admin.users.destroy', $author), [
            'delete_option' => 'reassign',
            'reassign_user_id' => $targetAdmin->id,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        // Author is deleted
        $this->assertDatabaseMissing('users', ['id' => $author->id]);

        // Posts & Page are preserved and attributed to targetAdmin
        $this->assertDatabaseHas('posts', [
            'id' => $post1->id,
            'user_id' => $targetAdmin->id,
        ]);
        $this->assertDatabaseHas('posts', [
            'id' => $post2->id,
            'user_id' => $targetAdmin->id,
        ]);
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'user_id' => $targetAdmin->id,
        ]);
    }

    public function test_deleting_user_defaults_to_reattributing_to_current_admin(): void
    {
        $currentAdmin = User::factory()->create([
            'name' => 'Logged Admin',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $author = User::factory()->create([
            'name' => 'Departing Author 2',
            'role' => 'author',
            'is_active' => true,
        ]);

        $post = Post::create([
            'user_id' => $author->id,
            'title' => 'Author Legacy Post',
            'slug' => 'author-legacy-post',
            'content' => '<p>Legacy content</p>',
            'status' => 'published',
        ]);

        // Request with no body parameters (default WordPress behavior)
        $response = $this->actingAs($currentAdmin)->delete(route('admin.users.destroy', $author));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $author->id]);

        // Post is preserved and attributed to currentAdmin
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'user_id' => $currentAdmin->id,
        ]);
    }

    public function test_deleting_user_with_delete_option_deletes_all_content(): void
    {
        $currentAdmin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $author = User::factory()->create([
            'name' => 'Author To Purge',
            'role' => 'author',
            'is_active' => true,
        ]);

        $post = Post::create([
            'user_id' => $author->id,
            'title' => 'Author Post To Delete',
            'slug' => 'author-post-to-delete',
            'content' => '<p>Post content</p>',
            'status' => 'published',
        ]);

        $response = $this->actingAs($currentAdmin)->delete(route('admin.users.destroy', $author), [
            'delete_option' => 'delete',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $author->id]);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_model_level_safety_net_reattributes_content_on_direct_user_delete(): void
    {
        $admin = User::factory()->create([
            'name' => 'System Admin',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $author = User::factory()->create([
            'name' => 'Direct Delete Author',
            'role' => 'author',
            'is_active' => true,
        ]);

        $post = Post::create([
            'user_id' => $author->id,
            'title' => 'Direct Delete Post',
            'slug' => 'direct-delete-post',
            'content' => '<p>Direct delete test</p>',
            'status' => 'published',
        ]);

        // Call direct Eloquent delete without controller
        $author->delete();

        $this->assertDatabaseMissing('users', ['id' => $author->id]);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'user_id' => $admin->id,
        ]);
    }

    public function test_database_backup_command_executes_successfully(): void
    {
        $backupPath = database_path('test_backup.sql');
        if (file_exists($backupPath)) {
            unlink($backupPath);
        }

        $this->artisan('db:backup', ['--filename' => 'test_backup.sql'])
            ->assertExitCode(0);

        $this->assertFileExists($backupPath);

        if (file_exists($backupPath)) {
            unlink($backupPath);
        }
    }

    public function test_guest_cannot_clear_cache(): void
    {
        $response = $this->post(route('admin.cache.clear'), ['type' => 'all']);
        $response->assertRedirect('/login');
    }

    public function test_admin_can_clear_all_system_caches(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->post(route('admin.cache.clear'), ['type' => 'all']);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success', 'All system caches (views, application data, routes, and config) cleared successfully.');
    }

    public function test_admin_can_clear_compiled_views_cache(): void
    {
        $editor = User::factory()->create([
            'role' => 'editor',
            'is_active' => true,
        ]);

        $response = $this->actingAs($editor)
            ->from(route('admin.dashboard'))
            ->post(route('admin.cache.clear'), ['type' => 'views']);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success', 'Compiled Blade views cleared successfully.');
    }

    public function test_dashboard_renders_clear_cache_buttons(): void
    {
        $editor = User::factory()->create([
            'role' => 'editor',
            'is_active' => true,
        ]);

        $response = $this->actingAs($editor)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee(route('admin.cache.clear'), false);
        $response->assertSee('Clear Cache');
        $response->assertSee('Purge All Caches');
    }
}

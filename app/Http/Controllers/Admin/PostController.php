<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\YoastSeoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    use HandlesFeaturedImageUpload;

    /**
     * Display a listing of posts.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('search');
        $categoryId = $request->query('category_id');

        $query = Post::with(['user', 'categories', 'tags'])->latest();

        if ($status && in_array($status, ['published', 'draft', 'scheduled'])) {
            $query->where('status', $status);
        }

        if ($categoryId) {
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }

        if ($search) {
            $query->search($search);
        }

        $posts = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        $counts = [
            'all' => Post::count(),
            'published' => Post::where('status', 'published')->count(),
            'draft' => Post::where('status', 'draft')->count(),
        ];

        return view('admin.posts.index', compact('posts', 'categories', 'status', 'search', 'categoryId', 'counts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'featured_image' => ['nullable'],
            'featured_image_file' => ['nullable', 'file', 'image', 'max:20480'],
            'status' => ['required', 'in:draft,published,scheduled'],
            'published_at' => ['nullable', 'date'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'tags_input' => ['nullable', 'string'],
            // SEO Meta fields
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string'],
            'og_image' => ['nullable', 'string'],
            'is_indexable' => ['nullable', 'boolean'],
            'focus_keyword' => ['nullable', 'string', 'max:255'],
            'seo_score' => ['nullable', 'integer'],
            'readability_score' => ['nullable', 'integer'],
            'schema_markup' => ['nullable', 'string'],
        ]);

        $featuredImage = $this->handleFeaturedImage($request, null, 'media/blog');

        $slug = ! empty($validated['slug'])
            ? Post::createUniqueSlug($validated['slug'])
            : Post::createUniqueSlug($validated['title']);

        $publishedAt = null;
        if ($validated['status'] === 'published') {
            $publishedAt = $validated['published_at'] ?? now();
        } elseif ($validated['status'] === 'scheduled') {
            $publishedAt = $validated['published_at'] ?? now()->addDay();
        }

        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'] ?? '',
            'featured_image' => $featuredImage,
            'status' => $validated['status'],
            'published_at' => $publishedAt,
            // SEO
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'canonical_url' => $validated['canonical_url'] ?? null,
            'og_title' => $validated['og_title'] ?? null,
            'og_description' => $validated['og_description'] ?? null,
            'og_image' => ! empty($validated['og_image']) ? $validated['og_image'] : $featuredImage,
            'is_indexable' => $request->has('is_indexable'),
            'focus_keyword' => $validated['focus_keyword'] ?? null,
            'seo_score' => (int) ($validated['seo_score'] ?? 0),
            'readability_score' => (int) ($validated['readability_score'] ?? 0),
            'schema_markup' => $validated['schema_markup'] ?? null,
        ]);

        // Attach categories
        if (! empty($validated['categories'])) {
            $post->categories()->sync($validated['categories']);
        }

        // Process Tags (comma-separated strings)
        $this->syncTagsFromInput($post, $request->input('tags_input'));

        return redirect()->route('admin.posts.edit', $post)
            ->with('success', "Post '{$post->title}' created successfully.");
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post): View
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $selectedCategories = $post->categories->pluck('id')->toArray();
        $tagsString = $post->tags->pluck('name')->implode(', ');

        return view('admin.posts.edit', compact('post', 'categories', 'tags', 'selectedCategories', 'tagsString'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug,'.$post->id],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'featured_image' => ['nullable'],
            'featured_image_file' => ['nullable', 'file', 'image', 'max:20480'],
            'status' => ['required', 'in:draft,published,scheduled'],
            'published_at' => ['nullable', 'date'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'tags_input' => ['nullable', 'string'],
            // SEO Meta fields
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string'],
            'og_image' => ['nullable', 'string'],
            'is_indexable' => ['nullable', 'boolean'],
            'focus_keyword' => ['nullable', 'string', 'max:255'],
            'seo_score' => ['nullable', 'integer'],
            'readability_score' => ['nullable', 'integer'],
            'schema_markup' => ['nullable', 'string'],
        ]);

        $featuredImage = $this->handleFeaturedImage($request, $post->featured_image, 'media/blog');

        $slug = ! empty($validated['slug'])
            ? Post::createUniqueSlug($validated['slug'], $post->id)
            : Post::createUniqueSlug($validated['title'], $post->id);

        $publishedAt = $post->published_at;
        if ($validated['status'] === 'published') {
            $publishedAt = $validated['published_at'] ?? ($post->published_at ?: now());
        } elseif ($validated['status'] === 'scheduled') {
            $publishedAt = $validated['published_at'] ?? now()->addDay();
        }

        $post->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'] ?? '',
            'featured_image' => $featuredImage,
            'status' => $validated['status'],
            'published_at' => $publishedAt,
            // SEO
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'canonical_url' => $validated['canonical_url'] ?? null,
            'og_title' => $validated['og_title'] ?? null,
            'og_description' => $validated['og_description'] ?? null,
            'og_image' => ! empty($validated['og_image']) ? $validated['og_image'] : ($post->og_image ?: $featuredImage),
            'is_indexable' => $request->has('is_indexable'),
            'focus_keyword' => $validated['focus_keyword'] ?? null,
            'seo_score' => (int) ($validated['seo_score'] ?? 0),
            'readability_score' => (int) ($validated['readability_score'] ?? 0),
            'schema_markup' => $validated['schema_markup'] ?? null,
        ]);

        // Sync categories
        $post->categories()->sync($validated['categories'] ?? []);

        // Sync tags
        $this->syncTagsFromInput($post, $request->input('tags_input'));

        return redirect()->route('admin.posts.edit', $post)
            ->with('success', "Post '{$post->title}' updated successfully.");
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $title = $post->title;
        $post->categories()->detach();
        $post->tags()->detach();
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', "Post '{$title}' deleted successfully.");
    }

    /**
     * Helper to parse comma separated tags and sync with post.
     */
    protected function syncTagsFromInput(Post $post, ?string $tagsInput): void
    {
        if (empty(trim($tagsInput ?? ''))) {
            $post->tags()->sync([]);

            return;
        }

        $tagNames = array_map('trim', explode(',', $tagsInput));
        $tagIds = [];

        foreach ($tagNames as $name) {
            if (empty($name)) {
                continue;
            }

            $tag = Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );

            $tagIds[] = $tag->id;
        }

        $post->tags()->sync($tagIds);
    }

    /**
     * Update focus keyword, meta description, and schema markup quickly from dashboard or index.
     */
    public function quickSeo(Request $request, Post $post): mixed
    {
        $validated = $request->validate([
            'focus_keyword' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'schema_markup' => ['nullable', 'string'],
            'seo_score' => ['nullable', 'integer'],
        ]);

        $score = $validated['seo_score'] ?? null;
        if ($score === null || $score <= 0) {
            $computed = YoastSeoService::calculateScores(
                $validated['focus_keyword'] ?? null,
                $post->title,
                $validated['meta_title'] ?? null,
                $validated['meta_description'] ?? null,
                $post->slug,
                $post->content
            );
            $score = $computed['seo_score'];
        }

        $post->update([
            'focus_keyword' => $validated['focus_keyword'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'schema_markup' => $validated['schema_markup'] ?? null,
            'seo_score' => min(100, max(0, (int) $score)),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "SEO & Schema for '{$post->title}' updated successfully.",
                'item' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'focus_keyword' => $post->focus_keyword,
                    'meta_title' => $post->meta_title,
                    'meta_description' => $post->meta_description,
                    'schema_markup' => $post->schema_markup,
                    'seo_score' => $post->seo_score,
                ],
            ]);
        }

        return redirect()->back()->with('success', "SEO updated for '{$post->title}'.");
    }
}

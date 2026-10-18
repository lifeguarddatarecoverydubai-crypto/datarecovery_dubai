<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\YoastSeoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    use HandlesFeaturedImageUpload;

    /**
     * Display a listing of pages.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Page::with('user')->latest();

        if ($status && in_array($status, ['published', 'draft'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $pages = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => Page::count(),
            'published' => Page::where('status', 'published')->count(),
            'draft' => Page::where('status', 'draft')->count(),
        ];

        return view('admin.pages.index', compact('pages', 'status', 'search', 'counts'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(): View
    {
        $templates = [
            'default' => 'Default Template',
            'full-width' => 'Full Width',
            'contact' => 'Contact Page',
        ];

        return view('admin.pages.create', compact('templates'));
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:pages,slug'],
            'content' => ['nullable', 'string'],
            'featured_image' => ['nullable'],
            'featured_image_file' => ['nullable', 'file', 'image', 'max:20480'],
            'template' => ['required', 'string', 'in:default,full-width,contact,home,about,fleet,services,industries,used-generators,locations,location-detail'],
            'status' => ['required', 'in:draft,published'],
            // SEO
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

        $featuredImage = $this->handleFeaturedImage($request, null, 'media/pages');

        $slug = ! empty($validated['slug'])
            ? Page::createUniqueSlug($validated['slug'])
            : Page::createUniqueSlug($validated['title']);

        $page = Page::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'] ?? '',
            'featured_image' => $featuredImage,
            'template' => $validated['template'],
            'status' => $validated['status'],
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

        return redirect()->route('admin.pages.edit', $page)
            ->with('success', "Page '{$page->title}' created successfully.");
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(Page $page): View
    {
        $templates = [
            'default' => 'Default Template',
            'full-width' => 'Full Width',
            'contact' => 'Contact Page',
            'home' => 'Homepage Template',
            'about' => 'About Us Template',
            'fleet' => 'Fleet Archive Template',
            'services' => 'Services Archive Template',
            'industries' => 'Industries Template',
            'used-generators' => 'Used Generators Template',
            'locations' => 'Locations Hub Template',
            'location-detail' => 'City Location Hub Template',
        ];

        return view('admin.pages.edit', compact('page', 'templates'));
    }

    /**
     * Update the specified page in storage.
     */
    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:pages,slug,'.$page->id],
            'content' => ['nullable', 'string'],
            'featured_image' => ['nullable'],
            'featured_image_file' => ['nullable', 'file', 'image', 'max:20480'],
            'template' => ['required', 'string', 'in:default,full-width,contact,home,about,fleet,services,industries,used-generators,locations,location-detail'],
            'status' => ['required', 'in:draft,published'],
            // SEO
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

        $featuredImage = $this->handleFeaturedImage($request, $page->featured_image, 'media/pages');

        $slug = ! empty($validated['slug'])
            ? Page::createUniqueSlug($validated['slug'], $page->id)
            : Page::createUniqueSlug($validated['title'], $page->id);

        $page->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'] ?? '',
            'featured_image' => $featuredImage,
            'template' => $validated['template'],
            'status' => $validated['status'],
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'canonical_url' => $validated['canonical_url'] ?? null,
            'og_title' => $validated['og_title'] ?? null,
            'og_description' => $validated['og_description'] ?? null,
            'og_image' => ! empty($validated['og_image']) ? $validated['og_image'] : ($page->og_image ?: $featuredImage),
            'is_indexable' => $request->has('is_indexable'),
            'focus_keyword' => $validated['focus_keyword'] ?? null,
            'seo_score' => (int) ($validated['seo_score'] ?? 0),
            'readability_score' => (int) ($validated['readability_score'] ?? 0),
            'schema_markup' => $validated['schema_markup'] ?? null,
        ]);

        return redirect()->route('admin.pages.edit', $page)
            ->with('success', "Page '{$page->title}' updated successfully.");
    }

    /**
     * Remove the specified page from storage.
     */
    public function destroy(Page $page): RedirectResponse
    {
        $title = $page->title;
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', "Page '{$title}' deleted successfully.");
    }

    /**
     * Update focus keyword, meta description, and schema markup quickly from dashboard or index.
     */
    public function quickSeo(Request $request, Page $page): mixed
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
                $page->title,
                $validated['meta_title'] ?? null,
                $validated['meta_description'] ?? null,
                $page->slug,
                $page->content
            );
            $score = $computed['seo_score'];
        }

        $page->update([
            'focus_keyword' => $validated['focus_keyword'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'schema_markup' => $validated['schema_markup'] ?? null,
            'seo_score' => min(100, max(0, (int) $score)),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "SEO & Schema for '{$page->title}' updated successfully.",
                'item' => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'focus_keyword' => $page->focus_keyword,
                    'meta_title' => $page->meta_title,
                    'meta_description' => $page->meta_description,
                    'schema_markup' => $page->schema_markup,
                    'seo_score' => $page->seo_score,
                ],
            ]);
        }

        return redirect()->back()->with('success', "SEO updated for '{$page->title}'.");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\YoastSeoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    use HandlesFeaturedImageUpload;

    /**
     * Display a listing of services.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Service::query()->ordered();

        if ($status && in_array($status, ['published', 'draft'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('tagline', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        $services = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => Service::count(),
            'published' => Service::where('status', 'published')->count(),
            'draft' => Service::where('status', 'draft')->count(),
        ];

        return view('admin.services.index', compact('services', 'status', 'search', 'counts'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(): View
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:services,slug'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'hero_badge' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'summary' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'features_input' => ['nullable', 'string'],
            'benefits_input' => ['nullable', 'string'],
            'benchmark_standard' => ['nullable', 'string', 'max:255'],
            'benchmark_evaluation' => ['nullable', 'string', 'max:255'],
            'benchmark_guarantee' => ['nullable', 'string', 'max:255'],
            'benchmark_turnaround' => ['nullable', 'string', 'max:255'],
            'benchmark_courier' => ['nullable', 'string', 'max:255'],
            'featured_image' => ['nullable'],
            'featured_image_file' => ['nullable', 'file', 'image', 'max:20480'],
            'status' => ['required', 'in:draft,published'],
            'order' => ['nullable', 'integer'],
            'is_featured' => ['nullable', 'boolean'],
            // SEO Meta fields
            'focus_keyword' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string'],
            'og_image' => ['nullable', 'string'],
            'is_indexable' => ['nullable', 'boolean'],
            'seo_score' => ['nullable', 'integer'],
            'readability_score' => ['nullable', 'integer'],
            'schema_markup' => ['nullable', 'string'],
        ]);

        $featuredImage = $this->handleFeaturedImage($request, null, 'media/services');

        $slug = ! empty($validated['slug'])
            ? Service::createUniqueSlug($validated['slug'])
            : Service::createUniqueSlug($validated['title']);

        // Parse features and benefits into arrays (one per line)
        $features = $this->parseLinesToArray($request->input('features_input'));
        $benefits = $this->parseLinesToArray($request->input('benefits_input'));

        $benchmarks = [
            'standard' => $request->input('benchmark_standard') ?: 'ISO-5 Class 100 Cleanroom & PC-3000 Hardware Tools',
            'evaluation' => $request->input('benchmark_evaluation') ?: 'Free In-Lab Diagnostics & Assessment',
            'guarantee' => $request->input('benchmark_guarantee') ?: 'No Data, No Recovery Fee Guarantee',
            'turnaround' => $request->input('benchmark_turnaround') ?: '24/7 Emergency Mobilization Across Dubai & UAE',
            'courier' => $request->input('benchmark_courier') ?: 'Free Express Courier Across All 7 Emirates',
        ];

        $service = Service::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'tagline' => $validated['tagline'] ?? null,
            'hero_badge' => $validated['hero_badge'] ?? null,
            'icon' => $validated['icon'] ?? 'sparkles',
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? '',
            'features' => $features,
            'benefits' => $benefits,
            'benchmarks' => $benchmarks,
            'featured_image' => $featuredImage,
            'status' => $validated['status'],
            'order' => (int) ($validated['order'] ?? 0),
            'is_featured' => $request->has('is_featured'),
            // SEO
            'focus_keyword' => $validated['focus_keyword'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'canonical_url' => $validated['canonical_url'] ?? null,
            'og_title' => $validated['og_title'] ?? null,
            'og_description' => $validated['og_description'] ?? null,
            'og_image' => ! empty($validated['og_image']) ? $validated['og_image'] : $featuredImage,
            'is_indexable' => $request->has('is_indexable'),
            'seo_score' => (int) ($validated['seo_score'] ?? 0),
            'readability_score' => (int) ($validated['readability_score'] ?? 0),
            'schema_markup' => $validated['schema_markup'] ?? null,
        ]);

        return redirect()->route('admin.services.edit', $service)
            ->with('success', "Service '{$service->title}' created successfully.");
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service): View
    {
        $featuresString = is_array($service->features) ? implode("\n", $service->features) : '';
        $benefitsString = is_array($service->benefits) ? implode("\n", $service->benefits) : '';
        $benchmarks = $service->effective_benchmarks;

        return view('admin.services.edit', compact('service', 'featuresString', 'benefitsString', 'benchmarks'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:services,slug,'.$service->id],
            'tagline' => ['nullable', 'string', 'max:255'],
            'hero_badge' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'summary' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'features_input' => ['nullable', 'string'],
            'benefits_input' => ['nullable', 'string'],
            'benchmark_standard' => ['nullable', 'string', 'max:255'],
            'benchmark_evaluation' => ['nullable', 'string', 'max:255'],
            'benchmark_guarantee' => ['nullable', 'string', 'max:255'],
            'benchmark_turnaround' => ['nullable', 'string', 'max:255'],
            'benchmark_courier' => ['nullable', 'string', 'max:255'],
            'featured_image' => ['nullable'],
            'featured_image_file' => ['nullable', 'file', 'image', 'max:20480'],
            'status' => ['required', 'in:draft,published'],
            'order' => ['nullable', 'integer'],
            'is_featured' => ['nullable', 'boolean'],
            // SEO Meta fields
            'focus_keyword' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string'],
            'og_image' => ['nullable', 'string'],
            'is_indexable' => ['nullable', 'boolean'],
            'seo_score' => ['nullable', 'integer'],
            'readability_score' => ['nullable', 'integer'],
            'schema_markup' => ['nullable', 'string'],
        ]);

        $featuredImage = $this->handleFeaturedImage($request, $service->featured_image, 'media/services');

        $slug = ! empty($validated['slug'])
            ? Service::createUniqueSlug($validated['slug'], $service->id)
            : Service::createUniqueSlug($validated['title'], $service->id);

        $features = $this->parseLinesToArray($request->input('features_input'));
        $benefits = $this->parseLinesToArray($request->input('benefits_input'));

        $benchmarks = [
            'standard' => $request->input('benchmark_standard') ?: 'ISO-5 Class 100 Cleanroom & PC-3000 Hardware Tools',
            'evaluation' => $request->input('benchmark_evaluation') ?: 'Free In-Lab Diagnostics & Assessment',
            'guarantee' => $request->input('benchmark_guarantee') ?: 'No Data, No Recovery Fee Guarantee',
            'turnaround' => $request->input('benchmark_turnaround') ?: '24/7 Emergency Mobilization Across Dubai & UAE',
            'courier' => $request->input('benchmark_courier') ?: 'Free Express Courier Across All 7 Emirates',
        ];

        $service->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'tagline' => $validated['tagline'] ?? null,
            'hero_badge' => $validated['hero_badge'] ?? null,
            'icon' => $validated['icon'] ?? 'sparkles',
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'] ?? '',
            'features' => $features,
            'benefits' => $benefits,
            'benchmarks' => $benchmarks,
            'featured_image' => $featuredImage,
            'status' => $validated['status'],
            'order' => (int) ($validated['order'] ?? 0),
            'is_featured' => $request->has('is_featured'),
            // SEO
            'focus_keyword' => $validated['focus_keyword'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'canonical_url' => $validated['canonical_url'] ?? null,
            'og_title' => $validated['og_title'] ?? null,
            'og_description' => $validated['og_description'] ?? null,
            'og_image' => ! empty($validated['og_image']) ? $validated['og_image'] : ($service->og_image ?: $featuredImage),
            'is_indexable' => $request->has('is_indexable'),
            'seo_score' => (int) ($validated['seo_score'] ?? 0),
            'readability_score' => (int) ($validated['readability_score'] ?? 0),
            'schema_markup' => $validated['schema_markup'] ?? null,
        ]);

        return redirect()->route('admin.services.edit', $service)
            ->with('success', "Service '{$service->title}' updated successfully.");
    }

    /**
     * Update focus keyword, meta description, and schema markup quickly from dashboard or index.
     */
    public function quickSeo(Request $request, Service $service): mixed
    {
        $validated = $request->validate([
            'focus_keyword' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'schema_markup' => ['nullable', 'string'],
            'seo_score' => ['nullable', 'integer'],
        ]);

        // Calculate realistic SEO score if not explicitly set
        $score = $validated['seo_score'] ?? null;
        if ($score === null || $score <= 0) {
            $computed = YoastSeoService::calculateScores(
                $validated['focus_keyword'] ?? null,
                $service->title,
                $validated['meta_title'] ?? null,
                $validated['meta_description'] ?? null,
                $service->slug,
                $service->content
            );
            $score = $computed['seo_score'];
        }

        $service->update([
            'focus_keyword' => $validated['focus_keyword'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'schema_markup' => $validated['schema_markup'] ?? null,
            'seo_score' => min(100, max(0, (int) $score)),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "SEO & Schema for '{$service->title}' updated successfully.",
                'service' => [
                    'id' => $service->id,
                    'title' => $service->title,
                    'focus_keyword' => $service->focus_keyword,
                    'meta_title' => $service->meta_title,
                    'meta_description' => $service->meta_description,
                    'schema_markup' => $service->schema_markup,
                    'seo_score' => $service->seo_score,
                ],
            ]);
        }

        return redirect()->back()
            ->with('success', "SEO & Schema settings for '{$service->title}' updated successfully.");
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service): RedirectResponse
    {
        $title = $service->title;
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', "Service '{$title}' removed successfully.");
    }

    /**
     * Helper to parse newline-separated string into clean array.
     */
    protected function parseLinesToArray(?string $input): array
    {
        if (! $input) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $input);
        $clean = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed !== '') {
                $clean[] = $trimmed;
            }
        }

        return $clean;
    }
}

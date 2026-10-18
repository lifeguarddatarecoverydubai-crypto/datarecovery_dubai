<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Service;
use App\Services\LocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the Locations Hub page featuring all branch offices.
     */
    public function locationsHub(): View
    {
        $locations = LocationService::all();
        $services = LocationService::getServicesCatalog();
        $uaeAreas = LocationService::getUaeAreaHubs();

        $page = Page::where('slug', 'locations')->first();
        if (! $page) {
            $page = new Page([
                'title' => 'Our Office Locations & Cleanroom Laboratories',
                'slug' => 'locations',
                'meta_title' => 'LifeGuard Data Recovery Locations | Dubai, Abu Dhabi, Sharjah, India & Kenya',
                'meta_description' => 'Find your nearest LifeGuard Data Recovery laboratory or drop-off office in Dubai, Abu Dhabi, Sharjah, India, and Kenya. Free evaluation and 24/7 emergency dispatch.',
                'status' => 'published',
                'is_indexable' => true,
            ]);
        }

        return view('frontend.pages.locations', compact('locations', 'services', 'uaeAreas', 'page'));
    }

    /**
     * Display a static CMS page or verified branch location page for LifeGuard Data Recovery.
     */
    public function show(string $slug): View|RedirectResponse
    {
        // 301 Redirect common alias permalinks to their canonical routes
        $canonicalRedirects = [
            'home' => url('/'),
            'about' => route('about'),
            'about-us' => route('about'),
            'services' => route('services.index'),
            'contact' => route('contact'),
            'contact-us' => route('contact'),
            'blog' => route('blog.index'),
            'articles' => route('blog.index'),
            'locations' => route('locations.index'),
            'all-locations' => route('locations.index'),
            'our-locations' => route('locations.index'),
            'location' => route('locations.index'),
            'data-recovery-kenya' => url('/data-recovery-service-in-kenya'),
        ];

        if (isset($canonicalRedirects[$slug])) {
            return redirect($canonicalRedirects[$slug], 301);
        }

        // If a published service exists with this slug, redirect to official service route to prevent cannibalization
        if ($service = Service::published()->where('slug', $slug)->first()) {
            return redirect('/'.$service->slug, 301);
        }

        // Handle location sub-paths if called via slug like locations/dubai
        if (str_starts_with($slug, 'locations/')) {
            $childSlug = substr($slug, 10);
            if (LocationService::isLocationSlug($childSlug)) {
                $matched = LocationService::find($childSlug);

                return redirect(url($matched['slug']), 301);
            }

            return redirect()->route('locations.index', [], 301);
        }

        // 1. Check if slug is an official branch location
        if ($location = LocationService::find($slug)) {
            $pageQuery = Page::with('user')->where('slug', $slug);
            if (! auth()->check() || ! auth()->user()->canManageContent()) {
                $pageQuery->where('status', 'published');
            }
            $page = $pageQuery->first();

            if (! $page) {
                $page = new Page([
                    'title' => $location['full_name'],
                    'slug' => $location['slug'],
                    'meta_title' => 'Data Recovery Services in '.$location['name'].' | LifeGuard Lab',
                    'meta_description' => 'Certified Data Recovery in '.$location['name'].'. Visit our office at '.$location['address'].' or call '.$location['phone'].'. Cleanroom recovery for HDD, SSD, RAID & mobile with No Data No Fee guarantee.',
                    'template' => 'location',
                    'status' => 'published',
                    'is_indexable' => true,
                ]);
            }

            $services = LocationService::getServicesCatalog();
            $otherLocations = LocationService::getOtherLocations($slug);
            $uaeAreas = LocationService::getUaeAreaHubs();

            return view('frontend.pages.location-detail', compact('page', 'location', 'services', 'otherLocations', 'uaeAreas'));
        }

        // 2. Standard CMS Page Query
        $pageQuery = Page::with('user')->where('slug', $slug);

        if (! auth()->check() || ! auth()->user()->canManageContent()) {
            $pageQuery->where('status', 'published');
        }

        $page = $pageQuery->firstOrFail();

        $viewName = match ($page->template) {
            'locations' => 'frontend.pages.locations',
            'location' => 'frontend.pages.location-detail',
            'full-width' => 'frontend.pages.full-width',
            'contact' => 'frontend.pages.contact',
            default => 'frontend.pages.show',
        };

        if ($viewName === 'frontend.pages.locations') {
            return $this->locationsHub();
        }

        if ($viewName === 'frontend.pages.location-detail') {
            $location = LocationService::find($slug) ?? LocationService::all()['dubai'];
            $services = LocationService::getServicesCatalog();
            $otherLocations = LocationService::getOtherLocations($slug);
            $uaeAreas = LocationService::getUaeAreaHubs();

            return view('frontend.pages.location-detail', compact('page', 'location', 'services', 'otherLocations', 'uaeAreas'));
        }

        return view($viewName, compact('page'));
    }
}

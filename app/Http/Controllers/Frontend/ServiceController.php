<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\SeoInternalLinker;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index(Request $request): View
    {
        $services = Service::published()->ordered()->get();

        return view('frontend.services.index', compact('services'));
    }

    /**
     * Display the specified service.
     */
    public function show(string $slug, ?SeoInternalLinker $linker = null): View
    {
        $linker = $linker ?? app(SeoInternalLinker::class);

        $service = Service::published()->where('slug', $slug)->firstOrFail();

        $otherServices = Service::published()
            ->where('id', '!=', $service->id)
            ->ordered()
            ->take(6)
            ->get();

        $relatedPosts = $linker->getRelatedPostsForService($service, 4);

        return view('frontend.services.show', compact('service', 'otherServices', 'relatedPosts'));
    }
}

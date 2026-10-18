<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the application homepage for LifeGuard Data Recovery.
     */
    public function index(): View
    {
        // 1. Data Recovery Services (Hard Drive, SSD, RAID, NAS, CCTV, Mobile, Mac, etc.)
        $services = Service::published()
            ->ordered()
            ->get();

        // 2. Featured Post & Latest Articles
        $featuredPost = Post::published()
            ->with(['user', 'categories'])
            ->whereNotNull('featured_image')
            ->latest('published_at')
            ->first();

        $featuredId = $featuredPost?->id;

        $latestPosts = Post::published()
            ->with(['user', 'categories', 'tags'])
            ->when($featuredId, fn ($q) => $q->where('id', '!=', $featuredId))
            ->latest('published_at')
            ->take(6)
            ->get();

        // 3. Blog Categories
        $categories = Category::whereHas('posts', fn ($q) => $q->where('status', 'published'))
            ->withCount(['posts' => fn ($q) => $q->where('status', 'published')])
            ->take(8)
            ->get();

        // 4. Homepage CMS page record if exists
        $page = Page::where('slug', 'home')->orWhere('slug', 'home-2')->first();

        return view('frontend.home', compact('services', 'featuredPost', 'latestPosts', 'categories', 'page'));
    }

    /**
     * About page showcasing LifeGuard Data Recovery laboratory, cleanroom certifications, and engineering history.
     */
    public function about(): View
    {
        $servicesCount = Service::published()->count();
        $postsCount = Post::published()->count();
        $page = Page::where('slug', 'about-us')->first();

        return view('frontend.about', compact('servicesCount', 'postsCount', 'page'));
    }
}

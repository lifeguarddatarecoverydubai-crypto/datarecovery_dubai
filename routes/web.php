<?php

use App\Http\Controllers\Admin\CacheController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OdooController as AdminOdooController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RedirectController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\OdooController as FrontendOdooController;
use App\Http\Controllers\Frontend\PageController as FrontendPageController;
use App\Http\Controllers\Frontend\ServiceController as FrontendServiceController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\RedirectRule;
use App\Models\Service;
use App\Models\Tag;
use App\Services\LocationService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about-us', [HomeController::class, 'about'])->name('about');
Route::redirect('/about', '/about-us', 301);

// Services Routes
Route::get('/services', [FrontendServiceController::class, 'index'])->name('services.index');
Route::get('/all-services', fn () => redirect('/services', 301))->name('service.index');
Route::get('/service/{slug}', fn (string $slug) => redirect('/'.$slug, 301))->name('service.show');
// Locations Routes
Route::get('/locations', [FrontendPageController::class, 'locationsHub'])->name('locations.index');
Route::redirect('/location', '/locations', 301);
Route::redirect('/all-locations', '/locations', 301);
Route::redirect('/our-locations', '/locations', 301);
Route::redirect('/data-recovery-kenya', '/data-recovery-service-in-kenya', 301);

// Blog & Content Routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/post/{slug}', fn (string $slug) => redirect('/'.$slug, 301));
Route::get('/category/{slug}', [BlogController::class, 'category'])->name('category.show');
Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('tag.show');
Route::get('/author/{slug}', [BlogController::class, 'author'])->name('author.show');
Route::get('/page/{slug}', [FrontendPageController::class, 'show'])->where('slug', '.*')->name('page.show');

// Contact
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::redirect('/contact-us', '/contact', 301);
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit')->middleware('throttle:10,1');
Route::post('/contact-us', [ContactController::class, 'submit'])->middleware('throttle:10,1');

// Odoo Partner Landing Page
Route::get('/odoo', [FrontendOdooController::class, 'index'])->name('odoo.index');

// XML Sitemaps (Master Unified Sitemap, Sitemap Index, & Section Sitemaps)
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap_index.xml', [SitemapController::class, 'sitemapIndex'])->name('sitemap.index');
Route::get('/sitemap-index.xml', [SitemapController::class, 'sitemapIndex']);
Route::get('/post-sitemap.xml', [SitemapController::class, 'posts'])->name('sitemap.posts');
Route::get('/sitemap-posts.xml', [SitemapController::class, 'posts']);
Route::get('/page-sitemap.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages']);
Route::get('/service-sitemap.xml', [SitemapController::class, 'services'])->name('sitemap.services');
Route::get('/sitemap-services.xml', [SitemapController::class, 'services']);
Route::get('/category-sitemap.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap-categories.xml', [SitemapController::class, 'categories']);
Route::get('/author-sitemap.xml', [SitemapController::class, 'authors'])->name('sitemap.authors');
Route::get('/sitemap-authors.xml', [SitemapController::class, 'authors']);

// Robots.txt
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:10,1');
});

// Google Two-Factor Authentication Flow
Route::get('/login/two-factor', [TwoFactorController::class, 'showChallenge'])->name('login.two-factor');
Route::post('/login/two-factor', [TwoFactorController::class, 'verifyChallenge'])->middleware('throttle:10,1');
Route::get('/login/two-factor-setup', [TwoFactorController::class, 'showSetup'])->name('login.two-factor.setup');
Route::post('/login/two-factor-setup', [TwoFactorController::class, 'confirmSetup'])->middleware('throttle:10,1');
Route::post('/login/two-factor/cancel', [TwoFactorController::class, 'cancel'])->name('login.two-factor.cancel');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin CMS Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'two_factor', 'content.manager'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard & Quick Draft
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/quick-draft', [DashboardController::class, 'quickDraft'])->name('quick-draft');

    // Odoo Partner Page Management (New)
    Route::get('/odoo', [AdminOdooController::class, 'edit'])->name('odoo.edit');
    Route::put('/odoo', [AdminOdooController::class, 'update'])->name('odoo.update');

    // Products Management (New)
    Route::resource('products', AdminProductController::class)->except(['show']);

    // Services Management (New)
    Route::patch('/services/{service}/quick-seo', [AdminServiceController::class, 'quickSeo'])->name('services.quick-seo');
    Route::resource('services', AdminServiceController::class)->except(['show']);

    // Leads & Client Inquiries (New)
    Route::get('/inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
    Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show'])->name('inquiries.show');
    Route::patch('/inquiries/{inquiry}/toggle-read', [InquiryController::class, 'toggleRead'])->name('inquiries.toggle-read');
    Route::delete('/inquiries/{inquiry}', [InquiryController::class, 'destroy'])->name('inquiries.destroy');

    // Posts Management
    Route::patch('/posts/{post}/quick-seo', [PostController::class, 'quickSeo'])->name('posts.quick-seo');
    Route::resource('posts', PostController::class)->except(['show']);

    // Categories Management
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

    // Tags Management
    Route::resource('tags', TagController::class)->except(['create', 'show', 'edit']);

    // Pages Management
    Route::patch('/pages/{page}/quick-seo', [AdminPageController::class, 'quickSeo'])->name('pages.quick-seo');
    Route::resource('pages', AdminPageController::class)->except(['show']);

    // Navigation Menus Management
    Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
    Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
    Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
    Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
    Route::patch('/menus/{menu}/toggle-active', [MenuController::class, 'toggleActive'])->name('menus.toggle-active');
    Route::post('/menus/{menu}/move-up', [MenuController::class, 'moveUp'])->name('menus.move-up');
    Route::post('/menus/{menu}/move-down', [MenuController::class, 'moveDown'])->name('menus.move-down');
    Route::post('/menus/reset-defaults', [MenuController::class, 'resetDefaults'])->name('menus.reset-defaults');

    // 301 & URL Redirection Management (WordPress-style, High Performance)
    Route::get('/redirects', [RedirectController::class, 'index'])->name('redirects.index');
    Route::post('/redirects', [RedirectController::class, 'store'])->name('redirects.store');
    Route::put('/redirects/{redirect}', [RedirectController::class, 'update'])->name('redirects.update');
    Route::delete('/redirects/{redirect}', [RedirectController::class, 'destroy'])->name('redirects.destroy');
    Route::patch('/redirects/{redirect}/toggle', [RedirectController::class, 'toggle'])->name('redirects.toggle');
    Route::post('/redirects/{redirect}/reset-hits', [RedirectController::class, 'resetHits'])->name('redirects.reset-hits');
    Route::post('/redirects/bulk', [RedirectController::class, 'bulk'])->name('redirects.bulk');
    Route::post('/redirects/test', [RedirectController::class, 'test'])->name('redirects.test');
    Route::get('/redirects/export', [RedirectController::class, 'exportCsv'])->name('redirects.export');
    Route::post('/redirects/import', [RedirectController::class, 'importCsv'])->name('redirects.import');
    Route::post('/redirects/seed-defaults', [RedirectController::class, 'seedDefaults'])->name('redirects.seed-defaults');

    // Media Library
    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/media', [MediaController::class, 'store'])->name('media.store');
    Route::put('/media/{medium}', [MediaController::class, 'update'])->name('media.update');
    Route::delete('/media/{medium}', [MediaController::class, 'destroy'])->name('media.destroy');
    Route::get('/media-modal-list', [MediaController::class, 'modalList'])->name('media.modal-list');

    // User Profile & Two-Factor Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/two-factor/recovery-codes', [ProfileController::class, 'regenerateRecoveryCodes'])->name('profile.two-factor.recovery-codes');
    Route::post('/profile/two-factor/reset', [ProfileController::class, 'resetTwoFactor'])->name('profile.two-factor.reset');

    // System Cache Management
    Route::post('/cache/clear', [CacheController::class, 'clear'])->name('cache.clear');

    // Administrator Only: User Management
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });
});

/*
|--------------------------------------------------------------------------
| Storage File Serving & Fallback Sync
|--------------------------------------------------------------------------
| Serves media files and syncs uploads between storage/app/public and public/storage
| ensuring uploaded images never 404 or 403 across shared hosting environments.
*/
Route::get('/storage/{path}', function (string $path) {
    $pathSegments = explode('/', str_replace('\\', '/', $path));
    if (in_array('..', $pathSegments, true) || in_array('.', $pathSegments, true) || str_contains($path, '\\')) {
        abort(404);
    }

    // 1. Direct check in public/storage
    $publicFile = public_path('storage/'.$path);
    if (is_file($publicFile)) {
        return response()->file($publicFile);
    }

    // 2. Check the versioned public media bundle used by fresh deployments
    $bundledFile = public_path('media-bundle/'.$path);
    if (is_file($bundledFile)) {
        return response()->file($bundledFile);
    }

    // 3. Check in storage/app/public
    $appPublicFile = storage_path('app/public/'.$path);
    if (is_file($appPublicFile)) {
        $targetDir = dirname($publicFile);
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        @copy($appPublicFile, $publicFile);

        return response()->file($appPublicFile);
    }

    abort(404);
})->where('path', '.*');

/*
|--------------------------------------------------------------------------
| Legacy WordPress Media & Uploads Redirects (Preserving Backlinks & Images)
|--------------------------------------------------------------------------
| Seamlessly maps legacy /wp-content/uploads/... URLs to modern WebP assets
| or resolves missing 404 images to a fallback asset with 301 redirects.
*/
Route::get('/wp-content/uploads/{path}', function (string $path) {
    $cleanPath = ltrim($path, '/');
    $webpPath = preg_replace('/\.(jpe?g|png|gif)$/i', '.webp', $cleanPath);

    if (strtolower($cleanPath) === '2022/03/aboutus.jpg') {
        return redirect(asset('media-bundle/media/pages/aboutus.webp'), 301);
    }

    if (is_file(public_path('storage/media/'.$webpPath)) || is_file(public_path('media-bundle/media/'.$webpPath))) {
        return redirect('/storage/media/'.$webpPath, 301);
    }

    if (is_file(public_path('storage/media/'.$cleanPath)) || is_file(public_path('media-bundle/media/'.$cleanPath))) {
        return redirect('/storage/media/'.$cleanPath, 301);
    }

    if (is_file(public_path('storage/'.$cleanPath)) || is_file(public_path('media-bundle/'.$cleanPath))) {
        return redirect('/storage/'.$cleanPath, 301);
    }

    return redirect('/images/logo.webp', 301);
})->where('path', '.*');

/*
|--------------------------------------------------------------------------
| Legacy WordPress Date Archive & Dated Post Redirects
|--------------------------------------------------------------------------
| 301-redirects legacy dated posts to root permalink, and thin date archives to /blog.
*/
Route::get('/{year}/{month}/{day}/{slug}', function (string $year, string $month, string $day, string $slug) {
    return redirect('/'.$slug, 301);
})->where(['year' => '20\d\d', 'month' => '\d{2}', 'day' => '\d{2}', 'slug' => '[a-zA-Z0-9\-_]+']);

Route::get('/{year}/{month}/{day}', function (string $year, string $month, string $day) {
    return redirect()->route('blog.index', [], 301);
})->where(['year' => '20\d\d', 'month' => '\d{2}', 'day' => '\d{2}']);

Route::get('/{year}/{month}', function (string $year, string $month) {
    return redirect()->route('blog.index', [], 301);
})->where(['year' => '20\d\d', 'month' => '\d{2}']);

/*
|--------------------------------------------------------------------------
| Dynamic Multi-Segment Permalinks (WordPress Sub-Page Parity)
|--------------------------------------------------------------------------
*/
Route::get('/{parent}/{slug}', function (string $parent, string $slug) {
    $fullSlug = $parent.'/'.$slug;

    // 1. Check Redirect Rules table
    if ($rule = RedirectRule::where('source_url', '/'.$fullSlug)->orWhere('source_url', '/'.$fullSlug.'/')->first()) {
        return redirect($rule->target_url, $rule->status_code);
    }

    // 2. Check Page by full slug or child slug
    if ($page = Page::where('slug', $fullSlug)->orWhere('slug', $slug)->where('status', 'published')->first()) {
        return app(FrontendPageController::class)->show($page->slug);
    }

    // 3. Check Post by child slug
    if ($post = Post::where('slug', $slug)->where('status', 'published')->first()) {
        return redirect('/'.$slug, 301);
    }

    abort(404);
})->where(['parent' => '[a-zA-Z0-9\-_]+', 'slug' => '[a-zA-Z0-9\-_]+']);

/*
|--------------------------------------------------------------------------
| Dynamic Slug Resolution (Direct WordPress Permalinks: /{slug})
|--------------------------------------------------------------------------
*/
Route::get('/{slug}', function (string $slug) {
    // 0. Canonical redirects
    $canonicalPermalinks = [
        'about' => route('about'),
        'contact-us' => route('contact'),
        'home' => route('home'),
        'home-2' => route('home'),
    ];

    if (isset($canonicalPermalinks[$slug])) {
        return redirect($canonicalPermalinks[$slug], 301);
    }

    // Fast-path cached type lookup (service, post, page, category, tag)
    $type = Cache::remember("slug_type_{$slug}", 3600, function () use ($slug) {
        if (Service::where('slug', $slug)->where('status', 'published')->exists()) {
            return 'service';
        }
        if (Post::where('slug', $slug)->where('status', 'published')->exists()) {
            return 'post';
        }
        if (Page::where('slug', $slug)->where('status', 'published')->exists()) {
            return 'page';
        }
        if (LocationService::isLocationSlug($slug)) {
            return 'page';
        }
        if (Category::where('slug', $slug)->exists()) {
            return 'category';
        }
        if (Tag::where('slug', $slug)->exists()) {
            return 'tag';
        }

        return null;
    });

    if ($type === 'service') {
        return app(FrontendServiceController::class)->show($slug);
    }
    if ($type === 'post') {
        return app(BlogController::class)->show($slug);
    }
    if ($type === 'page') {
        return app(FrontendPageController::class)->show($slug);
    }
    if ($type === 'category') {
        return app(BlogController::class)->category($slug);
    }
    if ($type === 'tag') {
        return app(BlogController::class)->tag($slug);
    }

    // Admin Preview for drafts
    if (auth()->check() && auth()->user()->canManageContent()) {
        if (Page::where('slug', $slug)->exists()) {
            return app(FrontendPageController::class)->show($slug);
        }
        if (Service::where('slug', $slug)->exists()) {
            return app(FrontendServiceController::class)->show($slug);
        }
        if (Post::where('slug', $slug)->exists()) {
            return app(BlogController::class)->show($slug);
        }
    }

    abort(404);
})->where('slug', '[a-zA-Z0-9\-_]+')->name('post.show');

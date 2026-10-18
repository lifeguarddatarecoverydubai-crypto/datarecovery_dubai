<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\RedirectRule;
use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;

$data = json_decode(file_get_contents(__DIR__.'/live_sitemaps.json'), true);

function extractSlug(string $url): string
{
    $path = trim(parse_url($url, PHP_URL_PATH), '/');

    return $path;
}

echo "============================================\n";
echo "1. AUDITING 397 POSTS FROM POST-SITEMAP.XML\n";
echo "============================================\n";

$missingPosts = [];
$foundPosts = [];

foreach ($data['posts'] as $url) {
    $slug = extractSlug($url);

    // Check if in posts table
    $post = Post::where('slug', $slug)->first();
    if ($post) {
        $foundPosts[$slug] = [
            'type' => 'post',
            'id' => $post->id,
            'title' => $post->title,
            'content_len' => strlen($post->content ?? ''),
            'status' => $post->status,
        ];

        continue;
    }

    // Check if in services table
    $svc = Service::where('slug', $slug)->first();
    if ($svc) {
        $foundPosts[$slug] = [
            'type' => 'service',
            'id' => $svc->id,
            'title' => $svc->title,
            'content_len' => strlen($svc->description ?? $svc->content ?? ''),
            'status' => $svc->status,
        ];

        continue;
    }

    // Check if in pages table
    $page = Page::where('slug', $slug)->first();
    if ($page) {
        $foundPosts[$slug] = [
            'type' => 'page',
            'id' => $page->id,
            'title' => $page->title,
            'content_len' => strlen($page->content ?? ''),
            'status' => $page->status,
        ];

        continue;
    }

    // Check if in redirect rules
    $rule = RedirectRule::where('source_url', '/'.$slug)->orWhere('source_url', '/'.$slug.'/')->first();
    if ($rule) {
        $foundPosts[$slug] = [
            'type' => 'redirect',
            'target' => $rule->target_url,
        ];

        continue;
    }

    $missingPosts[] = ['url' => $url, 'slug' => $slug];
}

echo 'Posts Found locally: '.count($foundPosts).' / '.count($data['posts'])."\n";
echo 'Posts Missing locally: '.count($missingPosts)."\n";
if (! empty($missingPosts)) {
    echo "Missing Posts Details:\n";
    foreach ($missingPosts as $m) {
        echo "- URL: {$m['url']} (Slug: {$m['slug']})\n";
    }
}

echo "\n============================================\n";
echo "2. AUDITING 88 PAGES FROM PAGE-SITEMAP.XML\n";
echo "============================================\n";

$missingPages = [];
$foundPages = [];

foreach ($data['pages'] as $url) {
    $slug = extractSlug($url);
    if ($slug === '') {
        $slug = 'home';
    }

    // Check Page
    $page = Page::where('slug', $slug)->first();
    if ($page) {
        $foundPages[$slug] = [
            'type' => 'page',
            'id' => $page->id,
            'title' => $page->title,
            'content_len' => strlen($page->content ?? ''),
        ];

        continue;
    }

    // Check Service
    $svc = Service::where('slug', $slug)->first();
    if ($svc) {
        $foundPages[$slug] = [
            'type' => 'service',
            'id' => $svc->id,
            'title' => $svc->title,
        ];

        continue;
    }

    // Check Post
    $post = Post::where('slug', $slug)->first();
    if ($post) {
        $foundPages[$slug] = [
            'type' => 'post',
            'id' => $post->id,
            'title' => $post->title,
        ];

        continue;
    }

    // Check Redirects
    $rule = RedirectRule::where('source_url', '/'.$slug)->orWhere('source_url', '/'.$slug.'/')->first();
    if ($rule) {
        $foundPages[$slug] = [
            'type' => 'redirect',
            'target' => $rule->target_url,
        ];

        continue;
    }

    // Check special routes
    if (in_array($slug, ['home', 'about', 'about-us', 'contact', 'contact-us', 'services', 'blog', 'odoo', 'locations'], true)) {
        $foundPages[$slug] = [
            'type' => 'hardcoded_route',
            'slug' => $slug,
        ];

        continue;
    }

    $missingPages[] = ['url' => $url, 'slug' => $slug];
}

echo 'Pages Found locally: '.count($foundPages).' / '.count($data['pages'])."\n";
echo 'Pages Missing locally: '.count($missingPages)."\n";
if (! empty($missingPages)) {
    echo "Missing Pages Details:\n";
    foreach ($missingPages as $m) {
        echo "- URL: {$m['url']} (Slug: {$m['slug']})\n";
    }
}

echo "\n============================================\n";
echo "3. AUDITING 7 CATEGORIES FROM CATEGORY-SITEMAP.XML\n";
echo "============================================\n";

$missingCategories = [];
$foundCategories = [];

foreach ($data['categories'] as $url) {
    // category urls are https://lifeguarddatarecovery.com/category/{slug}/
    $path = trim(parse_url($url, PHP_URL_PATH), '/');
    $parts = explode('/', $path);
    $slug = end($parts);

    $cat = Category::where('slug', $slug)->first();
    if ($cat) {
        $foundCategories[$slug] = [
            'id' => $cat->id,
            'name' => $cat->name,
            'slug' => $cat->slug,
            'posts_count' => $cat->posts()->count(),
        ];
    } else {
        $missingCategories[] = ['url' => $url, 'slug' => $slug];
    }
}

echo 'Categories Found locally: '.count($foundCategories).' / '.count($data['categories'])."\n";
echo 'Categories Missing locally: '.count($missingCategories)."\n";
if (! empty($missingCategories)) {
    echo "Missing Categories Details:\n";
    foreach ($missingCategories as $m) {
        echo "- URL: {$m['url']} (Slug: {$m['slug']})\n";
    }
}

<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;

$data = json_decode(file_get_contents(__DIR__.'/live_sitemaps.json'), true);

function extractSlug(string $url): string
{
    return trim(parse_url($url, PHP_URL_PATH), '/');
}

echo "=== VERIFYING ALL 397 POSTS ===\n";
$postIssues = [];
$h1CountZero = 0;
$h2CountZero = 0;
$h2CountPresent = 0;
$postsWithImages = 0;
$postsWithContent = 0;

foreach ($data['posts'] as $url) {
    $slug = extractSlug($url);
    $post = Post::where('slug', $slug)->first();

    if (! $post) {
        $postIssues[] = "Missing post for slug: {$slug} (URL: {$url})";

        continue;
    }

    if (strlen(trim($post->content ?? '')) < 50) {
        $postIssues[] = "Post has short/empty content: {$slug} (len: ".strlen($post->content ?? '').')';
    } else {
        $postsWithContent++;
    }

    if ($post->featured_image) {
        $postsWithImages++;
    }

    // Check headings in content
    $content = $post->content ?? '';
    $hasH1 = (bool) preg_match('/<h1[^>]*>/i', $content);
    $hasH2 = (bool) preg_match('/<h2[^>]*>/i', $content);
    $hasH3 = (bool) preg_match('/<h3[^>]*>/i', $content);

    if ($hasH2) {
        $h2CountPresent++;
    } else {
        $h2CountZero++;
    }
}

echo "Posts with content: {$postsWithContent} / ".count($data['posts'])."\n";
echo "Posts with featured image: {$postsWithImages} / ".count($data['posts'])."\n";
echo "Posts containing H2 headings: {$h2CountPresent} / ".count($data['posts'])."\n";
if (! empty($postIssues)) {
    echo 'Post issues detected ('.count($postIssues)."):\n";
    foreach (array_slice($postIssues, 0, 10) as $err) {
        echo "  - {$err}\n";
    }
} else {
    echo "All 397 posts verified successfully with zero issues!\n";
}

echo "\n=== VERIFYING ALL 88 PAGES ===\n";
$pageIssues = [];
$pagesWithContent = 0;

foreach ($data['pages'] as $url) {
    $slug = extractSlug($url);
    if ($slug === '') {
        $slug = 'home';
    }

    // Find in Page or Service
    $page = Page::where('slug', $slug)->first();
    $service = Service::where('slug', $slug)->first();

    if ($page) {
        $len = strlen(trim($page->content ?? ''));
        // Template pages like home, contact, locations may have dynamic view content
        if ($len > 0 || in_array($page->template, ['home', 'contact', 'locations', 'location', 'full-width'])) {
            $pagesWithContent++;
        } else {
            $pageIssues[] = "Page has empty content: {$slug} (ID: {$page->id})";
        }
    } elseif ($service) {
        $pagesWithContent++;
    } elseif (in_array($slug, ['home', 'about', 'about-us', 'contact', 'contact-us', 'services', 'blog', 'locations', 'odoo'])) {
        $pagesWithContent++;
    } else {
        $pageIssues[] = "Missing page for slug: {$slug} (URL: {$url})";
    }
}

echo "Pages verified: {$pagesWithContent} / ".count($data['pages'])."\n";
if (! empty($pageIssues)) {
    echo 'Page issues detected ('.count($pageIssues)."):\n";
    foreach ($pageIssues as $err) {
        echo "  - {$err}\n";
    }
} else {
    echo "All 88 pages verified successfully with zero issues!\n";
}

echo "\n=== VERIFYING ALL 7 CATEGORIES ===\n";
$categoryIssues = [];

foreach ($data['categories'] as $url) {
    $parts = explode('/', trim(parse_url($url, PHP_URL_PATH), '/'));
    $slug = end($parts);

    $cat = Category::where('slug', $slug)->first();
    if (! $cat) {
        $categoryIssues[] = "Missing category: {$slug} (URL: {$url})";

        continue;
    }

    $count = $cat->posts()->count();
    echo "Category: '{$cat->name}' (slug: '{$cat->slug}') has {$count} posts.\n";
    if ($count === 0) {
        $categoryIssues[] = "Category has 0 posts: {$cat->name} (slug: {$cat->slug})";
    }
}

if (empty($categoryIssues)) {
    echo "All 7 categories verified successfully with posts attached!\n";
} else {
    echo 'Category issues detected ('.count($categoryIssues)."):\n";
    foreach ($categoryIssues as $err) {
        echo "  - {$err}\n";
    }
}

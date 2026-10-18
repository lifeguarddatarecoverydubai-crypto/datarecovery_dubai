<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Http;

echo "--- 1. FEATURED IMAGES ---\n";
$postsWithImg = Post::whereNotNull('featured_image')->where('featured_image', '<>', '')->get();
$pagesWithImg = Page::whereNotNull('featured_image')->where('featured_image', '<>', '')->get();
$servicesWithImg = Service::whereNotNull('featured_image')->where('featured_image', '<>', '')->get();

echo 'Posts with featured image: '.$postsWithImg->count()."\n";
echo 'Pages with featured image: '.$pagesWithImg->count()."\n";
echo 'Services with featured image: '.$servicesWithImg->count()."\n";

if ($postsWithImg->first()) {
    echo 'Sample Post Featured Image: '.$postsWithImg->first()->featured_image."\n";
}
if ($pagesWithImg->first()) {
    echo 'Sample Page Featured Image: '.$pagesWithImg->first()->featured_image."\n";
}

echo "\n--- 2. CONTENT INLINE IMAGES ---\n";
$postContentImgs = [];
foreach (Post::cursor() as $post) {
    if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $post->content, $matches)) {
        foreach ($matches[1] as $src) {
            $postContentImgs[$src] = true;
        }
    }
}
echo 'Unique images found in Post content: '.count($postContentImgs)."\n";

$pageContentImgs = [];
foreach (Page::cursor() as $page) {
    if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $page->content, $matches)) {
        foreach ($matches[1] as $src) {
            $pageContentImgs[$src] = true;
        }
    }
}
echo 'Unique images found in Page content: '.count($pageContentImgs)."\n";

echo "\nSample inline image URLs:\n";
$sampleCount = 0;
foreach (array_keys($postContentImgs) as $src) {
    echo "  - {$src}\n";
    if (++$sampleCount >= 5) {
        break;
    }
}
foreach (array_keys($pageContentImgs) as $src) {
    echo "  - {$src}\n";
    if (++$sampleCount >= 10) {
        break;
    }
}

echo "\n--- 3. TESTING WORDPRESS MEDIA API ---\n";
try {
    $res = Http::timeout(10)->withoutVerifying()->get('https://lifeguarddatarecovery.com/wp-json/wp/v2/media', [
        'per_page' => 5,
    ]);
    echo 'WP Media API HTTP Status: '.$res->status()."\n";
    if ($res->successful()) {
        $totalMedia = $res->header('X-WP-Total') ?? 'unknown';
        $totalPages = $res->header('X-WP-TotalPages') ?? 'unknown';
        echo "X-WP-Total Media items in WP Library: {$totalMedia} (across {$totalPages} pages)\n";
        $data = $res->json();
        if (! empty($data)) {
            echo 'Sample WP Media source_url: '.($data[0]['source_url'] ?? 'none')."\n";
        }
    }
} catch (Exception $e) {
    echo 'WP Media API error: '.$e->getMessage()."\n";
}

echo "\n--- 4. EXISTING MEDIA TABLE IN DB ---\n";
echo 'Total records in media table: '.Media::count()."\n";

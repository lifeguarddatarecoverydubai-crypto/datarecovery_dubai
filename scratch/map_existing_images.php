<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Media;
use Illuminate\Contracts\Console\Kernel;

$json = file_get_contents(__DIR__.'/wp_posts_metadata.json');
$wpPosts = json_decode($json, true);

// Get all local files in storage/app/public/media
$dir = storage_path('app/public/media');
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile()) {
        $rel = str_replace(storage_path('app/public/'), '', $file->getPathname());
        $rel = str_replace('\\', '/', $rel);
        $basename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        $files[$basename] = $rel;
    }
}

echo 'Total local files indexed: '.count($files)."\n";

$matchedByWpUrl = 0;
$matchedBySlug = 0;
$matchedByMediaTable = 0;
$unmatched = 0;

$mappings = [];

foreach ($wpPosts as $slug => $data) {
    $wpImgUrl = $data['featured_image'];
    $wpImgBase = $wpImgUrl ? pathinfo(parse_url($wpImgUrl, PHP_URL_PATH), PATHINFO_FILENAME) : '';
    $slugClean = Str::slug($slug);

    $matchedPath = null;

    // 1. Check exact filename match in local files
    if ($wpImgBase && isset($files[$wpImgBase])) {
        $matchedPath = 'storage/'.$files[$wpImgBase];
        $matchedByWpUrl++;
    } elseif ($wpImgBase && isset($files[Str::slug($wpImgBase)])) {
        $matchedPath = 'storage/'.$files[Str::slug($wpImgBase)];
        $matchedByWpUrl++;
    } elseif (isset($files[$slugClean])) {
        $matchedPath = 'storage/'.$files[$slugClean];
        $matchedBySlug++;
    } else {
        // Check partial match with slug
        $found = false;
        foreach ($files as $base => $rel) {
            if (str_starts_with($slugClean, $base) || str_starts_with($base, $slugClean) || levenshtein(substr($slugClean, 0, 20), substr($base, 0, 20)) <= 3) {
                $matchedPath = 'storage/'.$rel;
                $matchedBySlug++;
                $found = true;
                break;
            }
        }
        if (! $found) {
            $unmatched++;
        }
    }

    $mappings[$slug] = [
        'slug' => $slug,
        'title' => $data['title'],
        'wp_img_url' => $wpImgUrl,
        'matched_local_path' => $matchedPath,
    ];
}

echo "Matched by WP image filename: $matchedByWpUrl\n";
echo "Matched by post slug: $matchedBySlug\n";
echo 'Total Matched: '.($matchedByWpUrl + $matchedBySlug)."\n";
echo "Unmatched: $unmatched\n";

file_put_contents(__DIR__.'/post_image_mappings.json', json_encode($mappings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

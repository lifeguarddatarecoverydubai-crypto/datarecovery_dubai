<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$postsMap = [];
$page = 1;

echo "=== FETCHING WP POSTS METADATA FROM LIVE WORDPRESS (4 pages) ===\n";

while ($page <= 5) {
    $url = "https://lifeguarddatarecovery.com/wp-json/wp/v2/posts?per_page=100&page={$page}&_embed=1";
    echo "Fetching page $page: $url ...\n";
    $start = microtime(true);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $time = round(microtime(true) - $start, 2);
    echo "  Response code: $code (took {$time}s)\n";

    if ($code !== 200 || empty($res)) {
        break;
    }

    $items = json_decode($res, true);
    if (empty($items) || ! is_array($items)) {
        break;
    }

    foreach ($items as $item) {
        $slug = $item['slug'];
        $featuredUrl = null;
        if (! empty($item['_embedded']['wp:featuredmedia'][0]['source_url'])) {
            $featuredUrl = $item['_embedded']['wp:featuredmedia'][0]['source_url'];
        } elseif (! empty($item['yoast_head_json']['og_image'][0]['url'])) {
            $featuredUrl = $item['yoast_head_json']['og_image'][0]['url'];
        }

        $postsMap[$slug] = [
            'id' => $item['id'],
            'slug' => $slug,
            'title' => html_entity_decode($item['title']['rendered'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'featured_image' => $featuredUrl,
            'content' => $item['content']['rendered'] ?? '',
            'content_length' => strlen($item['content']['rendered'] ?? ''),
        ];
    }

    echo '  Collected '.count($postsMap)." posts so far.\n";
    if (count($items) < 100) {
        break;
    }
    $page++;
}

file_put_contents(__DIR__.'/wp_posts_metadata.json', json_encode($postsMap, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "\nTotal WP posts collected: ".count($postsMap)."\n";
$withImage = count(array_filter($postsMap, fn ($p) => ! empty($p['featured_image'])));
echo "Posts with authentic featured image URL: $withImage\n";
echo "Saved to scratch/wp_posts_metadata.json\n";

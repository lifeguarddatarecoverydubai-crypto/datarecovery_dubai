<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "Fetching posts metadata from WordPress REST API...\n";

$page = 1;
$totalPosts = 0;
$postsWithWpFeatured = 0;
$samplePosts = [];

while (true) {
    $url = "https://lifeguarddatarecovery.com/wp-json/wp/v2/posts?per_page=100&page={$page}&_embed=1";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200 || empty($res)) {
        break;
    }

    $items = json_decode($res, true);
    if (empty($items) || ! is_array($items)) {
        break;
    }

    foreach ($items as $item) {
        $totalPosts++;
        $featuredUrl = null;
        if (! empty($item['_embedded']['wp:featuredmedia'][0]['source_url'])) {
            $featuredUrl = $item['_embedded']['wp:featuredmedia'][0]['source_url'];
        } elseif (! empty($item['yoast_head_json']['og_image'][0]['url'])) {
            $featuredUrl = $item['yoast_head_json']['og_image'][0]['url'];
        }

        if ($featuredUrl) {
            $postsWithWpFeatured++;
        }

        if (count($samplePosts) < 5) {
            $samplePosts[] = [
                'id' => $item['id'],
                'slug' => $item['slug'],
                'title' => $item['title']['rendered'],
                'featured_media' => $featuredUrl,
                'content_length' => strlen($item['content']['rendered']),
            ];
        }
    }

    echo "Page $page processed. Total so far: $totalPosts (with featured image: $postsWithWpFeatured)\n";
    if (count($items) < 100) {
        break;
    }
    $page++;
}

echo "\n=== SUMMARY OF LIVE WP POSTS ===\n";
echo "Total Posts fetched: $totalPosts\n";
echo "Posts with Featured Media on WP: $postsWithWpFeatured\n";
echo "Sample Posts:\n";
print_r($samplePosts);

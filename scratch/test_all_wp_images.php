<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Services\WebpConverterService;
use Illuminate\Contracts\Console\Kernel;

$json = file_get_contents(__DIR__.'/wp_posts_metadata.json');
$wpPosts = json_decode($json, true);

$uniqueUrls = [];
foreach ($wpPosts as $slug => $data) {
    if (! empty($data['featured_image'])) {
        $uniqueUrls[$data['featured_image']] = true;
    }
}
$urls = array_keys($uniqueUrls);
echo 'Testing '.count($urls)." unique WP image URLs with multi-curl...\n";

$mh = curl_multi_init();
$results = [];
$chunkSize = 30;
$chunks = array_chunk($urls, $chunkSize);

$converter = app(WebpConverterService::class);
$okCount = 0;
$failCount = 0;
$convertedCount = 0;

foreach ($chunks as $chunkIndex => $chunk) {
    $handles = [];
    foreach ($chunk as $url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_multi_add_handle($mh, $ch);
        $handles[$url] = $ch;
    }

    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh, 0.05);
    } while ($running > 0);

    foreach ($handles as $url => $ch) {
        $body = curl_multi_getcontent($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);

        if ($code === 200 && strlen($body) > 500) {
            $okCount++;
            $parsed = parse_url($url, PHP_URL_PATH);
            $basename = basename($parsed);
            $subfolder = 'media/blog';
            if (preg_match('#wp-content/uploads/(\d{4}/\d{2})#i', $parsed, $m)) {
                $subfolder = 'media/'.$m[1];
            }
            $res = $converter->convertBinary($body, $basename, $subfolder, 82, 1600);
            if ($res) {
                $results[$url] = 'storage/'.$res['file_path'];
                $convertedCount++;
            }
        } else {
            $failCount++;
        }
    }
    echo 'Chunk '.($chunkIndex + 1).'/'.count($chunks)." done. (OK: $okCount, Fail: $failCount, Converted: $convertedCount)\n";
}
curl_multi_close($mh);

file_put_contents(__DIR__.'/downloaded_wp_images.json', json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "\n=== SUMMARY ===\n";
echo 'Total tested: '.count($urls)."\n";
echo "HTTP 200 OK: $okCount\n";
echo "Failed / 404: $failCount\n";
echo "Converted to WebP: $convertedCount\n";
echo "Saved mapping to scratch/downloaded_wp_images.json\n";

<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Services\WebpConverterService;
use Illuminate\Contracts\Console\Kernel;

$testUrls = [
    'https://lifeguarddatarecovery.com/wp-content/uploads/2026/10/lifeguarddatarecovery-1-3.jpg',
    'https://lifeguarddatarecovery.com/wp-content/uploads/2026/10/lifeguarddatarecovery-1-2.jpg',
    'https://lifeguarddatarecovery.com/wp-content/uploads/2026/10/lifeguarddatarecovery-1-1.jpg',
    'https://lifeguarddatarecovery.com/wp-content/uploads/2026/10/lifeguarddatarecovery-.jpg',
    'https://lifeguarddatarecovery.com/wp-content/uploads/2026/09/lifeguarddatarecovery-1-23.jpg',
];

$mh = curl_multi_init();
$handles = [];

foreach ($testUrls as $i => $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_multi_add_handle($mh, $ch);
    $handles[$i] = ['handle' => $ch, 'url' => $url];
}

$running = null;
do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh, 0.1);
} while ($running > 0);

$converter = app(WebpConverterService::class);

foreach ($handles as $item) {
    $ch = $item['handle'];
    $url = $item['url'];
    $body = curl_multi_getcontent($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);

    echo "URL: $url | HTTP: $code | Bytes: ".strlen($body ?? '')."\n";
    if ($code === 200 && strlen($body) > 100) {
        $parsed = parse_url($url, PHP_URL_PATH);
        $basename = basename($parsed);
        $subfolder = 'media/2026/10';
        if (preg_match('#wp-content/uploads/(\d{4}/\d{2})#i', $parsed, $m)) {
            $subfolder = 'media/'.$m[1];
        }
        $res = $converter->convertBinary($body, $basename, $subfolder, 82, 1600);
        if ($res) {
            echo "  Converted to WebP: {$res['file_path']} ({$res['dimensions']}, {$res['size']} bytes)\n";
        }
    }
}
curl_multi_close($mh);
echo "=== Test Multi-Curl & WebP conversion complete! ===\n";

<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

echo "=== TESTING LOCAL POST AND SERVICE RENDERING ===\n";

$testUrls = [
    '/',
    '/hard-drive-data-recovery-dubai',
    '/ssd-drive-data-recovery-in-dubai',
    '/how-much-does-iphone-data-recovery-cost-in-abu-dhabi',
    '/crashed-operating-system-file-recovery-in-uae',
];

foreach ($testUrls as $uri) {
    $request = Request::create($uri, 'GET');
    $response = $app->handle($request);
    $status = $response->getStatusCode();
    $content = $response->getContent();

    preg_match_all('/<h[1-4][^>]*>(.*?)<\/h[1-4]>/i', $content, $headings);
    preg_match('/<img[^>]+src=["\']([^"\']+\.webp)["\']/i', $content, $imgMatch);

    echo "URI: $uri\n";
    echo "  Status: $status\n";
    echo '  Headings count: '.count($headings[0])."\n";
    if (! empty($imgMatch[1])) {
        echo '  First WebP Image: '.$imgMatch[1]."\n";
    }
    echo '  Response Length: '.strlen($content)." bytes\n";
    echo "----------------------------------------\n";
}

<?php

use GuzzleHttp\Client;

require __DIR__.'/../vendor/autoload.php';

$client = new Client(['verify' => false, 'timeout' => 20]);
$res = $client->get('https://lifeguarddatarecovery.com/', [
    'headers' => ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'],
]);
$html = $res->getBody()->getContents();

echo "--- FAVICONS FOUND IN HTML ---\n";
if (preg_match_all('/<link[^>]+(?:icon|apple-touch-icon)[^>]+>/i', $html, $matches)) {
    foreach ($matches[0] as $tag) {
        echo $tag."\n";
    }
}

echo "\n--- LOGOS FOUND IN HTML ---\n";
if (preg_match_all('/<img[^>]+(?:logo|brand)[^>]+>/i', $html, $matches)) {
    foreach ($matches[0] as $tag) {
        echo $tag."\n";
    }
}

// Also check Page featured_image or WP API for logo
echo "\n--- CHECKING KNOWN LOGO URLS ---\n";
$knownLogos = [
    'https://lifeguarddatarecovery.com/wp-content/uploads/2024/08/logo.png',
    'https://lifeguarddatarecovery.com/wp-content/uploads/2022/03/logo.png',
    'https://lifeguarddatarecovery.com/favicon.ico',
];
foreach ($knownLogos as $l) {
    try {
        $r = $client->head($l);
        echo "[{$r->getStatusCode()}] {$l} ({$r->getHeaderLine('Content-Type')})\n";
    } catch (Throwable $e) {
        echo "[ERR] {$l}\n";
    }
}

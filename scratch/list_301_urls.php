<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);

$data = json_decode(file_get_contents(__DIR__.'/live_sitemaps.json'), true);

function testUrl($kernel, string $url): array
{
    $path = parse_url($url, PHP_URL_PATH);
    if (! $path || $path === '') {
        $path = '/';
    }
    $request = Request::create($path, 'GET');
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    $target = $response->headers->get('Location');
    $kernel->terminate($request, $response);

    return ['path' => $path, 'status' => $status, 'target' => $target];
}

echo "=== 301 POST URLS ===\n";
foreach ($data['posts'] as $url) {
    $res = testUrl($kernel, $url);
    if ($res['status'] !== 200) {
        echo "- {$res['path']} => Status {$res['status']} -> {$res['target']}\n";
    }
}

echo "\n=== 301 PAGE URLS ===\n";
foreach ($data['pages'] as $url) {
    $res = testUrl($kernel, $url);
    if ($res['status'] !== 200) {
        echo "- {$res['path']} => Status {$res['status']} -> {$res['target']}\n";
    }
}

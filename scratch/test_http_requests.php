<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);

$urls = [
    '/author/dubailifeguard',
    '/author/muskan-dubai',
    '/author/ashar',
    '/data-recovery-abu-dhabi-2',
    '/raid-data-recovery-dubai',
    '/raid-data-recovery-service-dubai',
    '/author-sitemap.xml',
    '/page-sitemap.xml',
];

foreach ($urls as $uri) {
    $request = Request::create($uri, 'GET');
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    $target = $response->headers->get('Location') ?? '';
    echo "URL: {$uri} => Status {$status}".($target ? " (Redirects to: {$target})" : '')."\n";
    $kernel->terminate($request, $response);
}

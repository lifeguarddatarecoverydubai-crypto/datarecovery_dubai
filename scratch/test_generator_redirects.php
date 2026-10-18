<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

$slugsToTest = [
    '/rental-power',
    '/power-plant',
    '/amc-generator',
    '/maintenance',
    '/portacabin',
    '/we-offer',
    '/new-generators',
    '/used-refurbished-generators',
    '/hard-drive-data-recovery-dubai',
    '/ssd-drive-data-recovery-in-dubai',
    '/raid-data-recovery-service-dubai',
];

foreach ($slugsToTest as $uri) {
    $req = Request::create($uri, 'GET');
    $res = $app->handle($req);
    $status = $res->getStatusCode();
    $target = $res->headers->get('Location') ?? 'N/A';
    echo "URI: {$uri} => Status: {$status} | Target: {$target}\n";
}

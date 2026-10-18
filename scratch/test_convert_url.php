<?php

use App\Services\WebpConverterService;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$url = 'https://lifeguarddatarecovery.com/wp-content/uploads/2022/03/hard-drive-recovery.jpg';
$service = app(WebpConverterService::class);

echo "Calling convertUrl with error logging...\n";
$start = microtime(true);
$res = $service->convertUrl($url, 'media/services', 85, 1600);
$elapsed = round(microtime(true) - $start, 2);

echo "Elapsed: {$elapsed}s\n";
print_r($res);

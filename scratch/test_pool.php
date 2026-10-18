<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

$urls = [
    'https://lifeguarddatarecovery.com/wp-content/uploads/2026/09/lifeguarddatarecovery-1-20.jpg',
    'https://lifeguarddatarecovery.com/wp-content/uploads/2026/09/lifeguarddatarecovery-1-19.jpg',
    'https://lifeguarddatarecovery.com/wp-content/uploads/2024/08/logo.png',
];

$start = microtime(true);
$responses = Http::pool(fn (Pool $pool) => [
    $pool->as('img0')->withoutVerifying()->timeout(15)->get($urls[0]),
    $pool->as('img1')->withoutVerifying()->timeout(15)->get($urls[1]),
    $pool->as('img2')->withoutVerifying()->timeout(15)->get($urls[2]),
]);

$elapsed = round(microtime(true) - $start, 3);
echo "Http::pool finished in {$elapsed}s\n";
foreach ($responses as $key => $res) {
    echo "  {$key}: status=".($res->successful() ? 200 : 'failed').' bytes='.strlen($res->body())."\n";
}

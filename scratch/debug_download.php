<?php

use GuzzleHttp\Client;
use Illuminate\Contracts\Http\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$testUrl = 'https://lifeguarddatarecovery.com/wp-content/uploads/2026/10/lifeguarddatarecovery-1-3.jpg';

$client = new Client(['verify' => false, 'timeout' => 20]);
try {
    $res = $client->get($testUrl);
    echo 'Status: '.$res->getStatusCode()."\n";
    echo 'Content-Type: '.$res->getHeaderLine('Content-Type')."\n";
    echo 'Body length: '.strlen($res->getBody()->getContents())."\n";
} catch (Exception $e) {
    echo 'Guzzle Error: '.$e->getMessage()."\n";
}

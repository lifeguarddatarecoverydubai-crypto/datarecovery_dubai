<?php

use App\Services\WebpConverterService;
use Illuminate\Contracts\Http\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$service = new WebpConverterService;

$testUrl = 'https://lifeguarddatarecovery.com/wp-content/uploads/2024/08/logo.png';
echo "Testing conversion of PNG: {$testUrl}\n";

$result = $service->convertUrl($testUrl);

if ($result) {
    echo "SUCCESS!\n";
    echo json_encode($result, JSON_PRETTY_PRINT)."\n";
    echo 'App storage exists: '.(file_exists(storage_path('app/public/'.$result['file_path'])) ? 'YES' : 'NO')."\n";
    echo 'Public storage exists: '.(file_exists(public_path('storage/'.$result['file_path'])) ? 'YES' : 'NO')."\n";
} else {
    echo "FAILED to convert!\n";
}

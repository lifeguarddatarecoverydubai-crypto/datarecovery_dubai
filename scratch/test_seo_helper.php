<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Support\SeoHelper;
use Illuminate\Contracts\Console\Kernel;

$tests = [
    'how to recover important file from a non working ssd - LifeGuard Data Recovery' => 'How to Recover Important File From a Non Working SSD',
    'HOW Nand flash memory affects ssd performance and data recovery' => 'How NAND Flash Memory Affects SSD Performance and Data Recovery',
    'what causes ssd slowdowns and how to fix them | LifeGuard Data Recovery Dubai' => 'What Causes SSD Slowdowns and How to Fix Them',
    'cctv footage recovery from formatted dvr' => 'CCTV Footage Recovery From Formatted DVR',
    'macbook pro m1 ssd recovery in dubai - LifeGuard' => 'MacBook Pro M1 SSD Recovery in Dubai',
];

echo "--- TESTING TITLE FORMATTING ---\n";
foreach ($tests as $input => $expected) {
    $out = SeoHelper::formatTitle($input);
    $withBrand = SeoHelper::formatTitle($input, true);
    echo "Input:    $input\n";
    echo "Output:   $out\n";
    echo "Brand:    $withBrand\n\n";
}

echo "--- TESTING IMAGE URL RESOLUTION ---\n";
$images = [
    '/storage/storage/media/2022/03/logo.webp',
    'storage/media/services/ssd.webp',
    '/media/cleanroom.webp',
    'https://lifeguarddatarecovery.com/wp-content/uploads/about.jpg',
];
foreach ($images as $img) {
    echo "Image '$img' => ".SeoHelper::featuredImageUrl($img)."\n";
}

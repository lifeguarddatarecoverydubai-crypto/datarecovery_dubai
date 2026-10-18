<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use Illuminate\Contracts\Console\Kernel;

$checkSlugs = [
    'data-recovery-dubai',
    'data-recovery-al-barsha',
    'data-recovery-abu-dhabi',
    'data-recovery-sharjah',
    'data-recovery-al-wasl',
    'data-recovery-sheikh-zayed-road',
    'data-recovery-india',
    'data-recovery-service-in-kenya',
    'data-recovery-kenya',
    'locations',
];

foreach ($checkSlugs as $slug) {
    $exists = Page::where('slug', $slug)->exists();
    echo "Slug: {$slug} => ".($exists ? 'EXISTS' : 'MISSING')."\n";
}

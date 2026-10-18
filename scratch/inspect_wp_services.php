<?php

use App\Models\Page;
use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$serviceSlugs = [
    'hard-drive-data-recovery-dubai',
    'ssd-drive-data-recovery-in-dubai',
    'raid-data-recovery-service-dubai',
    'nas-data-recovery-dubai',
    'server-data-recovery-dubai',
    'cctv-footage-recovery-dubai',
    'sd-card-data-recovery',
    'iphone-data-recovery',
    'mac-data-recovery',
    'ransomware-data-recovery',
];

echo "=== CHECKING PAGES IN DB CORRESPONDING TO SERVICES ===\n";
foreach ($serviceSlugs as $slug) {
    $page = Page::where('slug', $slug)->first();
    $service = Service::where('slug', $slug)->first();

    echo "Slug: $slug\n";
    if ($page) {
        echo "  Page Title: {$page->title}\n";
        echo '  Page Content Length: '.strlen($page->content)."\n";
        echo "  Page Featured Image: {$page->featured_image}\n";
    } else {
        echo "  Page NOT in DB\n";
    }

    if ($service) {
        echo "  Service Title: {$service->title}\n";
        echo '  Service Content Length: '.strlen($service->content ?? '')."\n";
        echo "  Service Featured Image: {$service->featured_image}\n";
    } else {
        echo "  Service NOT in DB\n";
    }
    echo "----------------------------------------\n";
}

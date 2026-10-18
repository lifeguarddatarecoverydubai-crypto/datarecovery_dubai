<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\RedirectRule;
use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;

echo "--- 1. DELETING GENERATOR SERVICES ---\n";
$generatorSlugs = [
    'rental-power',
    'power-plant',
    'amc-generator',
    'maintenance',
    'portacabin',
    'we-offer',
    'new-generators',
    'used-refurbished-generators',
];

$deletedServices = Service::whereIn('slug', $generatorSlugs)->delete();
echo "Deleted {$deletedServices} generator services from `services` table.\n";

echo "\n--- 2. DELETING GENERATOR PRODUCTS ---\n";
$deletedProducts = Product::where('slug', 'like', '%kva%')
    ->orWhere('title', 'like', '%generator%')
    ->delete();
echo "Deleted {$deletedProducts} generator products from `products` table.\n";

echo "\n--- 3. ADDING 301 REDIRECTS FOR OLD GENERATOR URLS ---\n";
foreach ($generatorSlugs as $slug) {
    RedirectRule::updateOrCreate(
        ['source_url' => '/'.$slug],
        [
            'target_url' => '/services',
            'status_code' => 301,
            'is_active' => true,
        ]
    );
    RedirectRule::updateOrCreate(
        ['source_url' => '/'.$slug.'/'],
        [
            'target_url' => '/services',
            'status_code' => 301,
            'is_active' => true,
        ]
    );
}
echo "Added 301 redirect rules for all legacy generator URLs -> /services.\n";

echo "\n--- 4. RE-ORDERING DATA RECOVERY SERVICES ---\n";
$orderMap = [
    'hard-drive-data-recovery-dubai' => 1,
    'ssd-drive-data-recovery-in-dubai' => 2,
    'raid-data-recovery-dubai' => 3,
    'nas-data-recovery-dubai' => 4,
    'server-data-recovery-dubai' => 5,
    'cctv-footage-recovery-dubai' => 6,
    'sd-card-data-recovery' => 7,
    'iphone-data-recovery' => 8,
    'mac-data-recovery' => 9,
    'ransomware-data-recovery' => 10,
];

foreach ($orderMap as $slug => $order) {
    Service::where('slug', $slug)->update(['order' => $order]);
}

// Check if raid-data-recovery-service-dubai exists as page, sync slug if needed
$raidService = Service::where('slug', 'raid-data-recovery-dubai')->first();
if ($raidService) {
    $raidService->update(['slug' => 'raid-data-recovery-service-dubai']);
    // Also redirect old slug
    RedirectRule::updateOrCreate(
        ['source_url' => '/raid-data-recovery-dubai'],
        ['target_url' => '/raid-data-recovery-service-dubai', 'status_code' => 301, 'is_active' => true]
    );
    RedirectRule::updateOrCreate(
        ['source_url' => '/raid-data-recovery-dubai/'],
        ['target_url' => '/raid-data-recovery-service-dubai', 'status_code' => 301, 'is_active' => true]
    );
}

echo "Remaining active services in database:\n";
$remaining = Service::orderBy('order')->get();
foreach ($remaining as $s) {
    echo "ID: {$s->id} | Order: {$s->order} | Slug: {$s->slug} | Title: {$s->title}\n";
}

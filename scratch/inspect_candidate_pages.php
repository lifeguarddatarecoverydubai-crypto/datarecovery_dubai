<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;

$slugs = [
    'blog',
    'services',
    'locations',
    'data-recovery-abu-dhabi-2',
    'data-recovery-al-wasl',
    'data-recovery-in-abu-dhabi',
    'data-recovery-services-in-dubai',
    'data-recovery-engineer',
    'sales-executive',
    'social-media-marketing',
    'lifeguard-data-recovery-landing-page-ad',
    'server-data-recovery-ad',
    'seo',
    'raid-data-recovery-service-dubai',
];

echo "=== INSPECTING CANDIDATE PAGES ===\n";
foreach ($slugs as $slug) {
    $p = Page::where('slug', $slug)->first();
    if ($p) {
        $inMenu = MenuItem::where('url', 'like', "%{$slug}%")->count();
        echo "PAGE: [{$p->id}] '{$p->slug}' | Title: '{$p->title}' | Status: {$p->status} | Indexable: ".($p->is_indexable ? 'YES' : 'NO')." | InMenu: {$inMenu} | ContentLen: ".strlen($p->content ?? '')."\n";
    } else {
        echo "PAGE: '{$slug}' NOT FOUND\n";
    }
}

echo "\n=== CHECKING SERVICES WITH RAID SLUGS ===\n";
foreach (Service::where('slug', 'like', '%raid%')->get() as $s) {
    echo "SERVICE: [{$s->id}] '{$s->slug}' | Title: '{$s->title}'\n";
}

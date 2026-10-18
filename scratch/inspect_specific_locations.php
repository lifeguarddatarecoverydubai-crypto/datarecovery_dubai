<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use Illuminate\Contracts\Console\Kernel;

$slugs = [
    'data-recovery-dubai',
    'data-recovery-al-barsha',
    'data-recovery-abu-dhabi',
    'data-recovery-sharjah',
    'data-recovery-sheikh-zayed-road',
    'data-recovery-india',
    'data-recovery-service-in-kenya',
];

foreach ($slugs as $slug) {
    $p = Page::where('slug', $slug)->first();
    if ($p) {
        echo "=== SLUG: {$slug} (ID: {$p->id}) ===\n";
        echo "Title: {$p->title}\n";
        echo "SEO Title: {$p->seo_title}\n";
        echo "Template: {$p->template}\n";
        echo "Content preview (first 500 chars):\n";
        echo substr(strip_tags($p->content), 0, 500)."\n\n";
    } else {
        echo "=== SLUG: {$slug} NOT FOUND ===\n\n";
    }
}

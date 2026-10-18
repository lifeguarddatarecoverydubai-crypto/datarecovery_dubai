<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use Illuminate\Contracts\Console\Kernel;

echo "=== RAID PAGES ===\n";
foreach (Page::where('slug', 'like', '%raid%')->get() as $p) {
    echo "ID: {$p->id} | Slug: {$p->slug} | Title: {$p->title} | Length: ".strlen($p->content)."\n";
}

echo "=== CCTV PAGES ===\n";
foreach (Page::where('slug', 'like', '%cctv%')->orWhere('slug', 'like', '%footage%')->get() as $p) {
    echo "ID: {$p->id} | Slug: {$p->slug} | Title: {$p->title} | Length: ".strlen($p->content)."\n";
}

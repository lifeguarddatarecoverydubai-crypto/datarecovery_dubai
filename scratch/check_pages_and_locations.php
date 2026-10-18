<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

echo "=== PAGES ===\n";
foreach (Page::all() as $p) {
    echo "ID: {$p->id} | Slug: {$p->slug} | Title: {$p->title}\n";
}

echo "\n=== SERVICES ===\n";
foreach (Service::all() as $s) {
    echo "ID: {$s->id} | Slug: {$s->slug} | Name: {$s->title}\n";
}

echo "\n=== MENU ITEMS ===\n";
foreach (DB::table('menu_items')->get() as $m) {
    echo "ID: {$m->id} | Title: {$m->title} | URL: {$m->url} | Parent: {$m->parent_id}\n";
}

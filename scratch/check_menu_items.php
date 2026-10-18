<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\MenuItem;
use Illuminate\Contracts\Console\Kernel;

$items = MenuItem::all(['id', 'title', 'url', 'parent_id', 'order']);
foreach ($items as $item) {
    echo "ID {$item->id}: {$item->title} (URL: {$item->url}, Parent: {$item->parent_id})\n";
}

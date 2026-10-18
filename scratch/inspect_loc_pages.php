<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use Illuminate\Contracts\Console\Kernel;

$locPages = Page::where('slug', 'like', '%data-recovery%')
    ->orWhere('slug', 'like', '%dubai%')
    ->orWhere('slug', 'like', '%abu-dhabi%')
    ->orWhere('slug', 'like', '%sharjah%')
    ->orWhere('slug', 'like', '%kenya%')
    ->orWhere('slug', 'like', '%india%')
    ->get();

echo 'Found '.$locPages->count()." location-related pages in database:\n";
foreach ($locPages as $p) {
    echo sprintf("- ID: %d | Slug: %-35s | Title: %-35s | Length: %d chars | Template: %s\n",
        $p->id, $p->slug, substr($p->title, 0, 35), strlen($p->content ?? ''), $p->template ?? 'default');
}

<?php

use App\Models\Page;
use Illuminate\Contracts\Http\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$p = Page::where('slug', 'raid-data-recovery-service-dubai')->first();
if ($p) {
    echo "Title: {$p->title}\n";
    echo "Canonical in DB: '{$p->canonical_url}'\n";
} else {
    echo "Page not found in DB\n";
}

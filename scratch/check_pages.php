<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use Illuminate\Contracts\Console\Kernel;

$pages = Page::all();
echo 'Total Pages in DB: '.$pages->count()."\n";
$withImage = Page::whereNotNull('featured_image')->where('featured_image', '!=', '')->count();
echo "Pages with featured image: $withImage\n";

$samples = Page::limit(10)->get(['slug', 'title', 'featured_image']);
foreach ($samples as $p) {
    echo "Slug: {$p->slug} | Title: {$p->title} | Featured: {$p->featured_image}\n";
}

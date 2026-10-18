<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use Illuminate\Contracts\Console\Kernel;

$sample = Post::take(10)->get(['id', 'title', 'meta_title']);
foreach ($sample as $p) {
    echo "ID {$p->id}:\n";
    echo "  Title: {$p->title}\n";
    echo "  Meta:  {$p->meta_title}\n";
    echo "  Accessor seo_title: {$p->seo_title}\n\n";
}

// Check how many have lowercase title
$lowerCount = 0;
$doubleSuffixCount = 0;
foreach (Post::all() as $p) {
    if ($p->title && ctype_lower($p->title[0])) {
        $lowerCount++;
    }
    if (preg_match('/(LifeGuard Data Recovery).*?(LifeGuard Data Recovery)/i', $p->seo_title)) {
        $doubleSuffixCount++;
    }
}

echo "Total lowercase titles: $lowerCount / ".Post::count()."\n";
echo "Total double suffix in seo_title: $doubleSuffixCount / ".Post::count()."\n";

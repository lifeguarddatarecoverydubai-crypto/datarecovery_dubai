<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use Illuminate\Contracts\Console\Kernel;

$placeholderCount = 0;
$trailingBrandCount = 0;
$allPosts = Post::all();

foreach ($allPosts as $p) {
    if (in_array(strtolower(trim($p->meta_title ?? '')), ['seo meta description', 'seo meta title', 'meta title', 'title'], true)) {
        $placeholderCount++;
    }
    if (preg_match('/[-\|\—]\s*LifeGuard\s*Data\s*Recovery/i', $p->meta_title ?? '')) {
        $trailingBrandCount++;
    }
}

echo "Posts with placeholder meta_title: $placeholderCount\n";
echo "Posts with trailing brand in meta_title: $trailingBrandCount\n";

<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use Illuminate\Contracts\Console\Kernel;

$postsWithOgImage = Post::whereNotNull('og_image')->where('og_image', '!=', '')->count();
$postsWithDistinctOg = Post::distinct('og_image')->count('og_image');

echo "Posts with og_image: $postsWithOgImage\n";
echo "Distinct og_image values: $postsWithDistinctOg\n";

$samples = Post::whereNotNull('og_image')->where('og_image', '!=', '')->limit(10)->get(['slug', 'featured_image', 'og_image']);
foreach ($samples as $s) {
    echo "Slug: {$s->slug}\n";
    echo "  Featured: {$s->featured_image}\n";
    echo "  OG Image: {$s->og_image}\n";
}

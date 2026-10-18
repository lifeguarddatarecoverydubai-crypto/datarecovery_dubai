<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use Illuminate\Contracts\Console\Kernel;

$total = Post::count();
$genericFallback = Post::where('featured_image', 'like', '%lifeguarddatarecovery-1-20%')->count();
$emptyImage = Post::whereNull('featured_image')->orWhere('featured_image', '')->count();
$uniqueImages = Post::distinct('featured_image')->count('featured_image');

$withH2 = Post::where('content', 'like', '%<h2%')->count();
$withH3 = Post::where('content', 'like', '%<h3%')->count();
$withH4 = Post::where('content', 'like', '%<h4%')->count();
$withH1 = Post::where('content', 'like', '%<h1%')->count();

$avgLength = round(Post::selectRaw('AVG(LENGTH(content)) as avg_len')->first()->avg_len ?? 0);

echo "=== LOCAL POSTS AUDIT ===\n";
echo "Total Posts: $total\n";
echo "Posts with Generic Fallback Image: $genericFallback\n";
echo "Posts with Empty Image: $emptyImage\n";
echo "Distinct Featured Images: $uniqueImages\n";
echo "Posts containing <h1> in body: $withH1\n";
echo "Posts containing <h2> in body: $withH2\n";
echo "Posts containing <h3> in body: $withH3\n";
echo "Posts containing <h4> in body: $withH4\n";
echo "Average Content Length: $avgLength characters\n";

// Let's sample 3 posts
$samples = Post::inRandomOrder()->limit(3)->get(['slug', 'title', 'featured_image', 'content']);
foreach ($samples as $s) {
    echo "----------------------------------------\n";
    echo "Slug: {$s->slug}\n";
    echo "Title: {$s->title}\n";
    echo "Featured Image: {$s->featured_image}\n";
    echo 'Content Length: '.strlen($s->content)."\n";
    preg_match_all('/<h[1-4][^>]*>(.*?)<\/h[1-4]>/i', $s->content, $matches);
    echo 'Headings in content: '.count($matches[0])."\n";
    foreach (array_slice($matches[0], 0, 5) as $h) {
        echo '  '.strip_tags($h)."\n";
    }
}

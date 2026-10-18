<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use Illuminate\Contracts\Http\Kernel;

$total = Post::count();
$local = Post::where(function ($q) {
    $q->where('featured_image', 'like', '%storage%')
        ->orWhere('featured_image', 'like', 'media/%');
})->count();

$remote = Post::where('featured_image', 'like', 'http%')->count();

echo "Total blog posts: {$total}\n";
echo "Posts with local featured image: {$local}\n";
echo "Posts with remote HTTP featured image: {$remote}\n";

echo "\nSample 5 post featured_image values:\n";
foreach (Post::take(5)->pluck('featured_image') as $img) {
    echo "  - {$img}\n";
}

echo "\nSample 3 post featured_image_url accessor values:\n";
foreach (Post::take(3)->get() as $p) {
    echo "  - {$p->title} => {$p->featured_image_url}\n";
}

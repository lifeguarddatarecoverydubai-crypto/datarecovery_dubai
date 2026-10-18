<?php

use App\Models\Post;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$slug = 'what-causes-ssd-slowdowns-and-how-to-fix-them';
$post = Post::where('slug', $slug)->first();

if (! $post) {
    echo "Post not found!\n";
    exit;
}

echo 'Title: '.$post->title."\n";
echo 'Featured Image: '.$post->featured_image."\n";
echo 'Content Length: '.strlen($post->content)."\n";
echo "Content First 1000 chars:\n".substr($post->content, 0, 1000)."\n";

// Check headings in content
preg_match_all('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/i', $post->content, $headings);
echo 'Headings in DB content ('.count($headings[0])."):\n";
foreach ($headings[0] as $h) {
    echo '  '.strip_tags($h)."\n";
}

// Check images in content
preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $post->content, $images);
echo 'Images in DB content ('.count($images[1])."):\n";
foreach ($images[1] as $img) {
    echo '  '.$img."\n";
}

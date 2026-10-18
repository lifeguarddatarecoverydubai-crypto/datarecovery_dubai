<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use Illuminate\Contracts\Http\Kernel;

$featuredImages = [];
$contentImages = [];

foreach (Post::cursor() as $post) {
    if (! empty($post->featured_image)) {
        $featuredImages[$post->featured_image] = true;
    }
    if (! empty($post->og_image)) {
        $featuredImages[$post->og_image] = true;
    }
    if (! empty($post->content)) {
        if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $post->content, $matches)) {
            foreach ($matches[1] as $src) {
                $contentImages[$src] = true;
            }
        }
        if (preg_match_all('/srcset=["\']([^"\']+)["\']/i', $post->content, $matches)) {
            foreach ($matches[1] as $srcset) {
                foreach (explode(',', $srcset) as $part) {
                    $src = trim(explode(' ', trim($part))[0]);
                    $contentImages[$src] = true;
                }
            }
        }
    }
}

echo 'Total blog posts: '.Post::count()."\n";
echo 'Unique featured_image URLs in blogs: '.count($featuredImages)."\n";
echo 'Unique inline images in blog content: '.count($contentImages)."\n";

$allBlogImages = array_unique(array_merge(array_keys($featuredImages), array_keys($contentImages)));
echo 'Total distinct image URLs in all blogs: '.count($allBlogImages)."\n";

$sample = array_slice($allBlogImages, 0, 10);
echo "\nSample Blog Image URLs:\n";
foreach ($sample as $s) {
    echo "  - {$s}\n";
}

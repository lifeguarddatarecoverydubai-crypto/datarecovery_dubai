<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use Illuminate\Contracts\Console\Kernel;

$postsJson = file_get_contents(__DIR__.'/wp_posts_metadata.json');
$wpPosts = json_decode($postsJson, true);

$imagesJson = file_get_contents(__DIR__.'/downloaded_wp_images.json');
$downloaded = json_decode($imagesJson, true);

$sampleSlug = 'how-much-does-iphone-data-recovery-cost-in-abu-dhabi';
$wpData = $wpPosts[$sampleSlug];
$post = Post::where('slug', $sampleSlug)->first();

echo "Before update:\n";
echo "Title: {$post->title}\n";
echo "Featured: {$post->featured_image}\n";
echo 'Content length: '.strlen($post->content)."\n";

$authenticImg = $downloaded[$wpData['featured_image']] ?? null;
echo "Authentic WP Img URL: {$wpData['featured_image']}\n";
echo "Local Converted WebP: {$authenticImg}\n";

// Update content and replace any remote WP image URLs with local storage WebP
$updatedContent = $wpData['content'];
foreach ($downloaded as $remoteUrl => $localPath) {
    if (strpos($updatedContent, $remoteUrl) !== false) {
        $updatedContent = str_replace($remoteUrl, '/'.ltrim($localPath, '/'), $updatedContent);
    }
}

$post->title = $wpData['title'];
$post->content = $updatedContent;
if ($authenticImg) {
    $post->featured_image = '/'.ltrim($authenticImg, '/');
    $post->og_image = '/'.ltrim($authenticImg, '/');
}
$post->save();

echo "\nAfter update:\n";
echo "Title: {$post->title}\n";
echo "Featured: {$post->featured_image}\n";
echo 'Content length: '.strlen($post->content)."\n";
echo 'Featured Image URL accessor: '.$post->featured_image_url."\n";

preg_match_all('/<h[1-4][^>]*>(.*?)<\/h[1-4]>/i', $post->content, $matches);
echo 'Headings in content: '.count($matches[0])."\n";
foreach ($matches[0] as $h) {
    echo "  $h\n";
}

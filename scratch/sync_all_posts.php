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

echo "=== SYNCHRONIZING ALL 396 POSTS WITH AUTHENTIC CONTENT & IMAGES ===\n";

$updatedCount = 0;
$withAuthenticImage = 0;
$withSpecialFallback = 0;

foreach ($wpPosts as $slug => $data) {
    $post = Post::where('slug', $slug)->first();
    if (! $post) {
        continue;
    }

    $rawContent = $data['content'];

    // Replace any downloaded remote WordPress media URLs with local storage WebP
    foreach ($downloaded as $remoteUrl => $localPath) {
        if (strpos($rawContent, $remoteUrl) !== false) {
            $rawContent = str_replace($remoteUrl, '/'.ltrim($localPath, '/'), $rawContent);
        }
    }

    $wpImgUrl = $data['featured_image'];
    $assignedImg = null;

    if (! empty($wpImgUrl) && isset($downloaded[$wpImgUrl])) {
        $assignedImg = '/'.ltrim($downloaded[$wpImgUrl], '/');
        $withAuthenticImage++;
    } else {
        // Fallback for the 7 posts with missing WP attachments
        if (str_contains($slug, 'ssd') || str_contains($slug, 'nand')) {
            $assignedImg = '/storage/media/services/ssd-data-recovery-dubai.webp';
        } elseif (str_contains($slug, 'hdd') || str_contains($slug, 'hard-drive')) {
            $assignedImg = '/storage/media/services/hard-drive-recovery.webp';
        } else {
            $assignedImg = '/storage/media/2026/09/lifeguarddatarecovery-1-20.webp';
        }
        $withSpecialFallback++;
    }

    $post->title = $data['title'];
    $post->content = $rawContent;
    $post->featured_image = $assignedImg;
    $post->og_image = $assignedImg;
    $post->save();

    $updatedCount++;
}

echo "\nSynchronization Complete!\n";
echo "Total posts updated: $updatedCount\n";
echo "Posts with authentic converted WebP image: $withAuthenticImage\n";
echo "Posts with contextual service image: $withSpecialFallback\n";

// Audit unique images now
$uniqueImages = Post::distinct('featured_image')->count('featured_image');
echo "Total distinct featured images across all posts: $uniqueImages\n";

<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use App\Services\YoastSeoService;
use Illuminate\Contracts\Console\Kernel;

$sampleSlugs = [
    'hard-disk-repair-abu-dhabi',
    'data-services',
    'mac-data-recovery-services',
    'data-recovery-service-for-pc',
    'hard-drive-repair',
    'computer-frozen-after-showing-a-blue-screen',
    'hard-disk-repair-shop-near-me',
];

echo "=== TESTING YOAST SEO SERVICE ON USER SCREENSHOT POSTS ===\n";
foreach ($sampleSlugs as $slug) {
    $post = Post::where('slug', $slug)->first();
    if ($post) {
        $scores = YoastSeoService::calculateForModel($post);
        $rating = $scores['seo_score'] >= 80 ? 'good (GREEN)' : ($scores['seo_score'] >= 50 ? 'ok (YELLOW)' : 'poor (RED)');
        echo "Post: {$post->title}\n";
        echo "  Focus: '{$post->focus_keyword}'\n";
        echo "  SEO Score: {$scores['seo_score']}/100 -> {$rating}\n";
        echo "  Readability: {$scores['readability_score']}/100\n\n";
    } else {
        echo "Post with slug '{$slug}' not found.\n";
    }
}

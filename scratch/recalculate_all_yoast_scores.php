<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use App\Services\YoastSeoService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Cache;

echo "=== 1. CALCULATING YOAST SCORES FOR ALL POSTS (396) ===\n";
$postsUpdated = 0;
$scoresDist = ['good' => 0, 'ok' => 0, 'poor' => 0];

foreach (Post::all() as $post) {
    $scores = YoastSeoService::calculateForModel($post);
    $post->seo_score = $scores['seo_score'];
    $post->readability_score = $scores['readability_score'];
    $post->saveQuietly(); // save directly

    $rating = $post->seo_score >= 80 ? 'good' : ($post->seo_score >= 50 ? 'ok' : 'poor');
    $scoresDist[$rating]++;
    $postsUpdated++;
}
echo "Updated {$postsUpdated} Posts.\n";
echo "Score distribution: Good (>=80): {$scoresDist['good']} | OK (50-79): {$scoresDist['ok']} | Poor (<50): {$scoresDist['poor']}\n";

echo "\n=== 2. CALCULATING YOAST SCORES FOR ALL PAGES ===\n";
$pagesUpdated = 0;
$pageScoresDist = ['good' => 0, 'ok' => 0, 'poor' => 0];

foreach (Page::all() as $page) {
    $scores = YoastSeoService::calculateForModel($page);
    $page->seo_score = $scores['seo_score'];
    $page->readability_score = $scores['readability_score'];
    $page->saveQuietly();

    $rating = $page->seo_score >= 80 ? 'good' : ($page->seo_score >= 50 ? 'ok' : 'poor');
    $pageScoresDist[$rating]++;
    $pagesUpdated++;
}
echo "Updated {$pagesUpdated} Pages.\n";
echo "Score distribution: Good (>=80): {$pageScoresDist['good']} | OK (50-79): {$pageScoresDist['ok']} | Poor (<50): {$pageScoresDist['poor']}\n";

echo "\n=== 3. CALCULATING YOAST SCORES FOR ALL SERVICES ===\n";
$servicesUpdated = 0;
$svcScoresDist = ['good' => 0, 'ok' => 0, 'poor' => 0];

foreach (Service::all() as $service) {
    $scores = YoastSeoService::calculateForModel($service);
    $service->seo_score = $scores['seo_score'];
    $service->readability_score = $scores['readability_score'];
    $service->saveQuietly();

    $rating = $service->seo_score >= 80 ? 'good' : ($service->seo_score >= 50 ? 'ok' : 'poor');
    $svcScoresDist[$rating]++;
    $servicesUpdated++;
}
echo "Updated {$servicesUpdated} Services.\n";
echo "Score distribution: Good (>=80): {$svcScoresDist['good']} | OK (50-79): {$svcScoresDist['ok']} | Poor (<50): {$svcScoresDist['poor']}\n";

Cache::flush();
echo "\nCache cleared.\n";

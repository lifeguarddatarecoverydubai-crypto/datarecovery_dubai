<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use App\Support\SeoHelper;
use Illuminate\Contracts\Console\Kernel;

echo "=== FIXING POST TITLES & META TITLES ===\n";
$postsUpdated = 0;
foreach (Post::all() as $post) {
    $cleanTitle = SeoHelper::formatTitle($post->title, false);

    // If meta_title is empty or placeholder, use clean title
    $cleanMeta = $post->meta_title;
    if (empty($cleanMeta) || in_array(strtolower(trim($cleanMeta)), ['seo meta description', 'seo meta title', 'meta title', 'title'], true)) {
        $cleanMeta = $cleanTitle;
    } else {
        $cleanMeta = SeoHelper::formatTitle($cleanMeta, false);
    }

    if ($post->title !== $cleanTitle || $post->meta_title !== $cleanMeta) {
        $post->timestamps = false; // preserve original publish/update dates
        $post->title = $cleanTitle;
        $post->meta_title = $cleanMeta;
        $post->save();
        $postsUpdated++;
    }
}
echo "Updated $postsUpdated posts.\n";

echo "=== FIXING PAGE TITLES & META TITLES ===\n";
$pagesUpdated = 0;
foreach (Page::all() as $page) {
    $cleanTitle = SeoHelper::formatTitle($page->title, false);
    $cleanMeta = $page->meta_title ? SeoHelper::formatTitle($page->meta_title, false) : null;

    if ($page->title !== $cleanTitle || $page->meta_title !== $cleanMeta) {
        $page->timestamps = false;
        $page->title = $cleanTitle;
        if ($cleanMeta) {
            $page->meta_title = $cleanMeta;
        }
        $page->save();
        $pagesUpdated++;
    }
}
echo "Updated $pagesUpdated pages.\n";

echo "=== FIXING SERVICE TITLES & META TITLES ===\n";
$servicesUpdated = 0;
foreach (Service::all() as $service) {
    $cleanTitle = SeoHelper::formatTitle($service->title, false);
    $cleanMeta = $service->meta_title ? SeoHelper::formatTitle($service->meta_title, false) : null;

    if ($service->title !== $cleanTitle || $service->meta_title !== $cleanMeta) {
        $service->timestamps = false;
        $service->title = $cleanTitle;
        if ($cleanMeta) {
            $service->meta_title = $cleanMeta;
        }
        $service->save();
        $servicesUpdated++;
    }
}
echo "Updated $servicesUpdated services.\n";

echo "\nDone!\n";

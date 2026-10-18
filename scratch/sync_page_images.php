<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use Illuminate\Contracts\Console\Kernel;

$imagesJson = file_get_contents(__DIR__.'/downloaded_wp_images.json');
$downloaded = json_decode($imagesJson, true);

$updated = 0;
foreach (Page::all() as $page) {
    $rawContent = $page->content;
    $changed = false;

    // Replace downloaded remote images in content
    foreach ($downloaded as $remoteUrl => $localPath) {
        if (strpos($rawContent, $remoteUrl) !== false) {
            $rawContent = str_replace($remoteUrl, '/'.ltrim($localPath, '/'), $rawContent);
            $changed = true;
        }
    }

    if ($changed) {
        $page->content = $rawContent;
    }

    if (! empty($page->featured_image) && isset($downloaded[$page->featured_image])) {
        $page->featured_image = '/'.ltrim($downloaded[$page->featured_image], '/');
        $page->og_image = $page->featured_image;
        $changed = true;
    }

    if ($changed) {
        $page->save();
        $updated++;
    }
}

echo "Updated $updated pages with local WebP images and content links.\n";

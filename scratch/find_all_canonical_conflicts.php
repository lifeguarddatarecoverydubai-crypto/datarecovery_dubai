<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use App\Models\Page;
use App\Models\Post;
use Illuminate\Contracts\Http\Kernel;

echo "--- CHECKING PAGES CANONICAL CONFLICTS ---\n";
foreach (Page::cursor() as $page) {
    if (! empty($page->canonical_url)) {
        $path = trim(parse_url($page->canonical_url, PHP_URL_PATH) ?? '', '/');
        if ($path !== $page->slug) {
            echo "Page [/{$page->slug}] has canonical pointing to [/{$path}]\n";
        }
    }
}

echo "\n--- CHECKING POSTS CANONICAL CONFLICTS ---\n";
foreach (Post::cursor() as $post) {
    if (! empty($post->canonical_url)) {
        $path = trim(parse_url($post->canonical_url, PHP_URL_PATH) ?? '', '/');
        // If path has post/ or just slug
        $cleanPath = str_replace('post/', '', $path);
        if ($cleanPath !== $post->slug && $path !== $post->slug) {
            echo "Post [/{$post->slug}] has canonical pointing to [/{$path}]\n";
        }
    }
}

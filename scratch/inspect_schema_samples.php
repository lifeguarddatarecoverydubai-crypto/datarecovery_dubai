<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\Post;
use Illuminate\Contracts\Console\Kernel;

echo "=== SAMPLE POST SCHEMA ===\n";
$samplePost = Post::first();
echo 'Post ID: '.$samplePost->id.' | Title: '.$samplePost->title."\n";
echo "Schema:\n".substr($samplePost->schema_markup ?? '', 0, 400)."\n\n";

echo "=== SAMPLE PAGE SCHEMA ===\n";
$samplePage = Page::whereNotNull('schema_markup')->where('schema_markup', '!=', '')->first();
if ($samplePage) {
    echo 'Page ID: '.$samplePage->id.' | Title: '.$samplePage->title."\n";
    echo "Schema:\n".substr($samplePage->schema_markup ?? '', 0, 400)."\n\n";
}

echo "=== CHECKING BLADE SCHEMA RENDERING ===\n";
// Let's check view files for x-schema-json-ld or json-ld

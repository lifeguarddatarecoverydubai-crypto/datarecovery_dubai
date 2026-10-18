<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use Illuminate\Contracts\Console\Kernel;

$json = file_get_contents(__DIR__.'/wp_posts_metadata.json');
$wpPosts = json_decode($json, true);

$matched = 0;
$unmatched = 0;
$missingSlugs = [];

foreach ($wpPosts as $slug => $data) {
    $p = Post::where('slug', $slug)->first();
    if ($p) {
        $matched++;
    } else {
        $unmatched++;
        $missingSlugs[] = $slug;
    }
}

echo 'Total WP Posts: '.count($wpPosts)."\n";
echo "Matched in local Post table: $matched\n";
echo "Unmatched in local Post table: $unmatched\n";
if ($unmatched > 0) {
    print_r($missingSlugs);
}

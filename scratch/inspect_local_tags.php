<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\Console\Kernel;

echo 'Local tags count: '.Tag::count()."\n";
echo 'Posts with at least 1 tag: '.Post::has('tags')->count()."\n";
echo 'Posts with 0 tags: '.Post::doesntHave('tags')->count()."\n";

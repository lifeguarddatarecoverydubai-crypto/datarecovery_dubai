<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;

echo "=== LOCAL DATABASE COUNTS ===\n";
echo 'Posts count: '.Post::count()."\n";
echo 'Published posts count: '.Post::where('status', 'published')->count()."\n";
echo 'Pages count: '.Page::count()."\n";
echo 'Published pages count: '.Page::where('status', 'published')->count()."\n";
echo 'Categories count: '.Category::count()."\n";
echo 'Services count: '.Service::count()."\n";

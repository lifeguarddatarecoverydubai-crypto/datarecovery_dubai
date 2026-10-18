<?php

use App\Models\Page;
use App\Models\Post;
use Illuminate\Contracts\Http\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$postH1 = 0;
foreach (Post::cursor() as $p) {
    if (preg_match('/<h1[^>]*>/i', $p->content)) {
        $postH1++;
    }
}
$pageH1 = 0;
foreach (Page::cursor() as $p) {
    if (preg_match('/<h1[^>]*>/i', $p->content)) {
        $pageH1++;
    }
}
echo "Posts with h1 in content: {$postH1}\n";
echo "Pages with h1 in content: {$pageH1}\n";

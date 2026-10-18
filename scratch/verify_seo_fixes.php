<?php

use App\Models\Post;
use Illuminate\Http\Request;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$req = Request::create('/fleet/50-kva', 'GET');
$res = $app->handle($req);
$html = $res->getContent();

preg_match('/<meta name="description" content="(.*?)">/', $html, $m);
echo '50-kva Meta Description: '.($m[1] ?? 'NOT FOUND')."\n";

preg_match('/<link rel="canonical" href="(.*?)">/', $html, $c);
echo '50-kva Canonical: '.($c[1] ?? 'NOT FOUND')."\n";

// Also test a blog post canonical
$post = Post::first();
if ($post) {
    $reqPost = Request::create('/post/'.$post->slug, 'GET');
    $resPost = $app->handle($reqPost);
    preg_match('/<link rel="canonical" href="(.*?)">/', $resPost->getContent(), $cp);
    echo "Sample Post ({$post->slug}) Canonical: ".($cp[1] ?? 'NOT FOUND')."\n";
}

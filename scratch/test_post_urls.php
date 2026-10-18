<?php

use App\Models\Post;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);

$slug = 'what-causes-ssd-slowdowns-and-how-to-fix-them';

// 1. Test root permalink
$req1 = Request::create('/'.$slug, 'GET');
$res1 = $kernel->handle($req1);
echo 'GET /'.$slug.' => '.$res1->getStatusCode()."\n";

// 2. Test /post/{slug}
$req2 = Request::create('/post/'.$slug, 'GET');
$res2 = $kernel->handle($req2);
echo 'GET /post/'.$slug.' => '.$res2->getStatusCode()."\n";
if ($res2->isRedirection()) {
    echo 'Redirect target: '.$res2->headers->get('Location')."\n";
}

echo "route('post.show', \$slug) => ".route('post.show', $slug)."\n";
$post = Post::where('slug', $slug)->first();
echo "route('post.show', \$post) => ".route('post.show', $post)."\n";
echo '$post->url => '.$post->url."\n";

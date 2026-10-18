<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);

$data = json_decode(file_get_contents(__DIR__.'/live_sitemaps.json'), true);

function testUrl($kernel, string $url): array
{
    $path = parse_url($url, PHP_URL_PATH);
    if (! $path || $path === '') {
        $path = '/';
    }

    $request = Request::create($path, 'GET');
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    $kernel->terminate($request, $response);

    return [
        'url' => $url,
        'path' => $path,
        'status' => $status,
        'location' => $response->headers->get('Location'),
    ];
}

echo "=== TESTING ALL 397 POST URLS ===\n";
$postResults = ['200' => 0, '301' => 0, '404' => 0, 'other' => 0];
$postErrors = [];

foreach ($data['posts'] as $url) {
    $res = testUrl($kernel, $url);
    if ($res['status'] === 200) {
        $postResults['200']++;
    } elseif ($res['status'] === 301 || $res['status'] === 302) {
        $postResults['301']++;
    } elseif ($res['status'] === 404) {
        $postResults['404']++;
        $postErrors[] = $res;
    } else {
        $postResults['other']++;
        $postErrors[] = $res;
    }
}
echo 'Post URL Results: '.json_encode($postResults)."\n";
if (! empty($postErrors)) {
    echo "Post Errors:\n";
    foreach ($postErrors as $e) {
        echo "- {$e['path']} => Status {$e['status']}\n";
    }
}

echo "\n=== TESTING ALL 88 PAGE URLS ===\n";
$pageResults = ['200' => 0, '301' => 0, '404' => 0, 'other' => 0];
$pageErrors = [];

foreach ($data['pages'] as $url) {
    $res = testUrl($kernel, $url);
    if ($res['status'] === 200) {
        $pageResults['200']++;
    } elseif ($res['status'] === 301 || $res['status'] === 302) {
        $pageResults['301']++;
    } elseif ($res['status'] === 404) {
        $pageResults['404']++;
        $pageErrors[] = $res;
    } else {
        $pageResults['other']++;
        $pageErrors[] = $res;
    }
}
echo 'Page URL Results: '.json_encode($pageResults)."\n";
if (! empty($pageErrors)) {
    echo "Page Errors:\n";
    foreach ($pageErrors as $e) {
        echo "- {$e['path']} => Status {$e['status']}\n";
    }
}

echo "\n=== TESTING ALL 7 CATEGORY URLS ===\n";
$catResults = ['200' => 0, '301' => 0, '404' => 0, 'other' => 0];
$catErrors = [];

foreach ($data['categories'] as $url) {
    $res = testUrl($kernel, $url);
    if ($res['status'] === 200) {
        $catResults['200']++;
    } elseif ($res['status'] === 301 || $res['status'] === 302) {
        $catResults['301']++;
    } elseif ($res['status'] === 404) {
        $catResults['404']++;
        $catErrors[] = $res;
    } else {
        $catResults['other']++;
        $catErrors[] = $res;
    }
}
echo 'Category URL Results: '.json_encode($catResults)."\n";
if (! empty($catErrors)) {
    echo "Category Errors:\n";
    foreach ($catErrors as $e) {
        echo "- {$e['path']} => Status {$e['status']}\n";
    }
}

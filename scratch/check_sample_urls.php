<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\Post;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Http\Kernel;

$client = new Client(['verify' => false, 'timeout' => 10]);

// Collect some URLs from posts
$sampleUrls = [];
foreach (Post::whereNotNull('featured_image')->take(10)->pluck('featured_image') as $u) {
    $sampleUrls[] = $u;
}
foreach (Page::whereNotNull('featured_image')->take(5)->pluck('featured_image') as $u) {
    $sampleUrls[] = $u;
}

echo "Testing sample URLs:\n";
foreach ($sampleUrls as $url) {
    try {
        $res = $client->head($url, ['headers' => ['User-Agent' => 'Mozilla/5.0']]);
        echo " [{$res->getStatusCode()}] {$url}\n";
    } catch (ClientException $e) {
        echo " [{$e->getResponse()->getStatusCode()}] {$url}\n";
    } catch (Throwable $e) {
        echo " [ERR] {$url} - {$e->getMessage()}\n";
    }
}

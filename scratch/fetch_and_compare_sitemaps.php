<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use Illuminate\Contracts\Console\Kernel;

function fetchSitemapUrls(string $url): array
{
    echo "Fetching {$url}...\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $xmlContent = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200 || ! $xmlContent) {
        echo "Failed to fetch {$url} (HTTP {$code})\n";

        return [];
    }

    $urls = [];
    $xml = @simplexml_load_string($xmlContent);
    if ($xml) {
        foreach ($xml->url as $urlElement) {
            $loc = trim((string) $urlElement->loc);
            if ($loc) {
                $urls[] = $loc;
            }
        }
    } else {
        // Fallback regex in case of XML parse warning
        preg_match_all('/<loc>(.*?)<\/loc>/', $xmlContent, $matches);
        if (! empty($matches[1])) {
            $urls = array_map('trim', $matches[1]);
        }
    }

    return $urls;
}

$postUrls = fetchSitemapUrls('https://lifeguarddatarecovery.com/post-sitemap.xml');
$pageUrls = fetchSitemapUrls('https://lifeguarddatarecovery.com/page-sitemap.xml');
$categoryUrls = fetchSitemapUrls('https://lifeguarddatarecovery.com/category-sitemap.xml');

echo "Fetched counts from live sitemaps:\n";
echo '- Posts from sitemap: '.count($postUrls)."\n";
echo '- Pages from sitemap: '.count($pageUrls)."\n";
echo '- Categories from sitemap: '.count($categoryUrls)."\n";

file_put_contents(__DIR__.'/live_sitemaps.json', json_encode([
    'posts' => $postUrls,
    'pages' => $pageUrls,
    'categories' => $categoryUrls,
], JSON_PRETTY_PRINT));

echo "Saved live sitemaps to scratch/live_sitemaps.json\n";

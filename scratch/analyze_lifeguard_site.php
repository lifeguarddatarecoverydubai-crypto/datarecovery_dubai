<?php

$ctx = stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) CMS-Migrator/2.0\r\nAccept: application/json, text/xml, */*\r\n",
        'timeout' => 30,
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
]);

echo "=== Analyzing https://lifeguarddatarecovery.com/ ===\n";

// 1. Fetch sitemap index
$indexXml = @file_get_contents('https://lifeguarddatarecovery.com/sitemap_index.xml', false, $ctx);
if (! $indexXml) {
    exit("Failed to fetch sitemap_index.xml\n");
}

$xml = simplexml_load_string($indexXml);
$subSitemaps = [];
foreach ($xml->sitemap as $s) {
    $subSitemaps[] = (string) $s->loc;
}

echo 'Sub-sitemaps found ('.count($subSitemaps)."):\n";
foreach ($subSitemaps as $sm) {
    echo "  - {$sm}\n";
}

$allUrlsByType = [];
foreach ($subSitemaps as $sm) {
    $smContent = @file_get_contents($sm, false, $ctx);
    if (! $smContent) {
        echo "Failed to fetch {$sm}\n";

        continue;
    }
    $smXml = simplexml_load_string($smContent);
    $name = basename($sm);
    $urls = [];
    if (isset($smXml->url)) {
        foreach ($smXml->url as $u) {
            $urls[] = (string) $u->loc;
        }
    }
    $allUrlsByType[$name] = $urls;
    echo "  [{$name}] found ".count($urls)." URLs\n";
}

// 2. Check WordPress REST API for Posts, Pages, Categories, Tags, Media
$endpoints = [
    'posts' => 'https://lifeguarddatarecovery.com/wp-json/wp/v2/posts?per_page=1',
    'pages' => 'https://lifeguarddatarecovery.com/wp-json/wp/v2/pages?per_page=1',
    'categories' => 'https://lifeguarddatarecovery.com/wp-json/wp/v2/categories?per_page=100',
    'tags' => 'https://lifeguarddatarecovery.com/wp-json/wp/v2/tags?per_page=100',
    'users' => 'https://lifeguarddatarecovery.com/wp-json/wp/v2/users?per_page=100',
    'media' => 'https://lifeguarddatarecovery.com/wp-json/wp/v2/media?per_page=1',
];

echo "\n=== WordPress REST API Details ===\n";
foreach ($endpoints as $type => $url) {
    $res = @file_get_contents($url, false, $ctx);
    if ($res) {
        $total = 'unknown';
        foreach ($http_response_header as $h) {
            if (stripos($h, 'X-WP-Total:') !== false) {
                $total = trim(substr($h, stripos($h, ':') + 1));
            }
        }
        $data = json_decode($res, true);
        $countInResponse = is_array($data) ? count($data) : 0;
        echo "  - {$type}: Total available on WP = {$total} (Fetched in 1 page: {$countInResponse})\n";
    } else {
        echo "  - {$type}: FAILED\n";
    }
}

// Save summary of sitemap URLs to json for analysis
file_put_contents('scratch/sitemap_urls_summary.json', json_encode($allUrlsByType, JSON_PRETTY_PRINT));
echo "\nSaved sitemap URLs summary to scratch/sitemap_urls_summary.json\n";

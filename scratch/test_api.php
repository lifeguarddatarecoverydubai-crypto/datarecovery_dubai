<?php

$ctx = stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\nAccept: application/json, text/xml, */*\r\n",
        'timeout' => 20,
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
]);

echo "--- 1. Testing Sitemap Index ---\n";
$sitemap = @file_get_contents('https://lifeguarddatarecovery.com/sitemap_index.xml', false, $ctx);
if ($sitemap) {
    echo 'Sitemap index retrieved! Length: '.strlen($sitemap)." bytes\n";
    echo substr($sitemap, 0, 800)."\n...\n";
} else {
    echo "Sitemap index failed, trying /sitemap.xml\n";
    $sitemap = @file_get_contents('https://lifeguarddatarecovery.com/sitemap.xml', false, $ctx);
    if ($sitemap) {
        echo 'sitemap.xml retrieved! Length: '.strlen($sitemap)." bytes\n";
    }
}

echo "\n--- 2. Testing WordPress REST API Types ---\n";
$types = @file_get_contents('https://lifeguarddatarecovery.com/wp-json/wp/v2/types', false, $ctx);
if ($types) {
    $typesJson = json_decode($types, true);
    echo 'Post Types found: '.implode(', ', array_keys($typesJson ?? []))."\n";
} else {
    echo "Types endpoint failed\n";
}

echo "\n--- 3. Testing Posts Header ---\n";
$posts = @file_get_contents('https://lifeguarddatarecovery.com/wp-json/wp/v2/posts?per_page=1', false, $ctx);
if ($posts) {
    foreach ($http_response_header as $h) {
        if (stripos($h, 'X-WP-Total') !== false) {
            echo 'Posts: '.$h."\n";
        }
    }
} else {
    echo 'Posts endpoint failed: '.($http_response_header[0] ?? 'No header')."\n";
}

echo "\n--- 4. Testing Pages Header ---\n";
$pages = @file_get_contents('https://lifeguarddatarecovery.com/wp-json/wp/v2/pages?per_page=1', false, $ctx);
if ($pages) {
    foreach ($http_response_header as $h) {
        if (stripos($h, 'X-WP-Total') !== false) {
            echo 'Pages: '.$h."\n";
        }
    }
} else {
    echo 'Pages endpoint failed: '.($http_response_header[0] ?? 'No header')."\n";
}

echo "\n--- 5. Testing Taxonomies ---\n";
$tax = @file_get_contents('https://lifeguarddatarecovery.com/wp-json/wp/v2/taxonomies', false, $ctx);
if ($tax) {
    $taxJson = json_decode($tax, true);
    echo 'Taxonomies: '.implode(', ', array_keys($taxJson ?? []))."\n";
}

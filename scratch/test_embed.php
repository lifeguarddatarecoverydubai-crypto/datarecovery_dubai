<?php

$ctx = stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\nAccept: application/json\r\n",
        'timeout' => 25,
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
]);

echo "Testing single post with _embed...\n";
$json = @file_get_contents('https://lifeguarddatarecovery.com/wp-json/wp/v2/posts?per_page=1&_embed=1', false, $ctx);
if ($json) {
    $posts = json_decode($json, true);
    if (! empty($posts[0])) {
        $p = $posts[0];
        echo 'Post Title: '.($p['title']['rendered'] ?? '')."\n";
        echo 'Slug: '.($p['slug'] ?? '')."\n";
        echo 'Link: '.($p['link'] ?? '')."\n";
        echo 'Has Yoast: '.(isset($p['yoast_head_json']) ? 'YES' : 'NO')."\n";
        if (isset($p['yoast_head_json'])) {
            echo '  Yoast Title: '.($p['yoast_head_json']['title'] ?? '')."\n";
            echo '  Yoast Desc: '.($p['yoast_head_json']['description'] ?? '')."\n";
            echo '  Yoast Canonical: '.($p['yoast_head_json']['canonical'] ?? '')."\n";
        }
        $featured = $p['_embedded']['wp:featuredmedia'][0]['source_url'] ?? 'none';
        echo 'Featured Image: '.$featured."\n";
        $author = $p['_embedded']['author'][0]['name'] ?? 'none';
        echo 'Author: '.$author."\n";
    }
} else {
    echo "Failed to fetch post\n";
}

echo "\nTesting single page with _embed...\n";
$pageJson = @file_get_contents('https://lifeguarddatarecovery.com/wp-json/wp/v2/pages?per_page=1&_embed=1', false, $ctx);
if ($pageJson) {
    $pages = json_decode($pageJson, true);
    if (! empty($pages[0])) {
        $pg = $pages[0];
        echo 'Page Title: '.($pg['title']['rendered'] ?? '')."\n";
        echo 'Slug: '.($pg['slug'] ?? '')."\n";
        echo 'Link: '.($pg['link'] ?? '')."\n";
        echo 'Has Yoast: '.(isset($pg['yoast_head_json']) ? 'YES' : 'NO')."\n";
    }
}

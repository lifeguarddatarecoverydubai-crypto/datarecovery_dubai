<?php

$url = 'https://lifeguarddatarecovery.com/wp-json/wp/v2/posts?per_page=5&page=1&_embed=1';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$res = curl_exec($ch);
curl_close($ch);

$posts = json_decode($res, true);
foreach ($posts as $p) {
    echo 'Slug: '.$p['slug']."\n";
    echo 'Title: '.$p['title']['rendered']."\n";
    $fm = $p['_embedded']['wp:featuredmedia'][0]['source_url'] ?? 'none';
    $og = $p['yoast_head_json']['og_image'][0]['url'] ?? 'none';
    echo "  Featured Media: $fm\n";
    echo "  Yoast OG Image: $og\n";
    echo "----------------------------------------\n";
}

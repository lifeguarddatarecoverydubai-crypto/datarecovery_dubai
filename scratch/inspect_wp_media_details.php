<?php

$url = 'https://lifeguarddatarecovery.com/wp-json/wp/v2/posts?per_page=2&page=1&_embed=1';
$res = file_get_contents($url);
$data = json_decode($res, true);

foreach ($data as $p) {
    echo 'Slug: '.$p['slug']."\n";
    echo 'Featured Media ID: '.$p['featured_media']."\n";
    if (! empty($p['_embedded']['wp:featuredmedia'][0])) {
        $fm = $p['_embedded']['wp:featuredmedia'][0];
        echo 'Source URL: '.($fm['source_url'] ?? '')."\n";
        echo "Media Details sizes:\n";
        if (! empty($fm['media_details']['sizes'])) {
            foreach ($fm['media_details']['sizes'] as $sizeName => $sizeInfo) {
                echo "  $sizeName: ".($sizeInfo['source_url'] ?? '').' ('.($sizeInfo['width'] ?? '').'x'.($sizeInfo['height'] ?? '').")\n";
            }
        }
    }
    echo "----------------------------------------\n";
}

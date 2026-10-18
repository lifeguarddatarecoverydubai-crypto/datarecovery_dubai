<?php

use GuzzleHttp\Client;

require __DIR__.'/../vendor/autoload.php';

$client = new Client(['verify' => false, 'timeout' => 15]);
$res = $client->get('https://lifeguarddatarecovery.com/wp-json/wp/v2/posts?per_page=3&_embed=1');
$posts = json_decode($res->getBody()->getContents(), true);

foreach ($posts as $p) {
    echo 'Post ID: '.$p['id']."\n";
    echo '  Title: '.$p['title']['rendered']."\n";
    echo '  Featured Media ID: '.($p['featured_media'] ?? 0)."\n";
    if (! empty($p['_embedded']['wp:featuredmedia'])) {
        $fm = $p['_embedded']['wp:featuredmedia'][0];
        echo '  Embedded Media source_url: '.($fm['source_url'] ?? 'none')."\n";
        if (isset($fm['media_details']['sizes'])) {
            echo '  Available sizes: '.implode(', ', array_keys($fm['media_details']['sizes']))."\n";
            foreach ($fm['media_details']['sizes'] as $sName => $sInfo) {
                echo "    {$sName}: ".($sInfo['source_url'] ?? '')."\n";
            }
        }
    }
}

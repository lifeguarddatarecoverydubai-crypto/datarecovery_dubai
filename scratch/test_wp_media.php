<?php

use GuzzleHttp\Client;

require __DIR__.'/../vendor/autoload.php';
$client = new Client(['verify' => false, 'timeout' => 15]);
$res = $client->get('https://lifeguarddatarecovery.com/wp-json/wp/v2/media?per_page=10&page=1');
$data = json_decode($res->getBody()->getContents(), true);
foreach ($data as $idx => $item) {
    echo "Item {$idx}:\n";
    echo '  id: '.$item['id']."\n";
    echo '  date: '.$item['date']."\n";
    echo '  source_url: '.$item['source_url']."\n";
    echo '  mime_type: '.$item['mime_type']."\n";
    if (isset($item['media_details']['sizes'])) {
        echo '  sizes available: '.implode(', ', array_keys($item['media_details']['sizes']))."\n";
    }
}

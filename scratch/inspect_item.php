<?php

use GuzzleHttp\Client;

require __DIR__.'/../vendor/autoload.php';
$client = new Client(['verify' => false, 'timeout' => 15]);
$res = $client->get('https://lifeguarddatarecovery.com/wp-json/wp/v2/media/11811');
$data = json_decode($res->getBody()->getContents(), true);
echo "Media Details for 11811:\n";
print_r($data['media_details']);

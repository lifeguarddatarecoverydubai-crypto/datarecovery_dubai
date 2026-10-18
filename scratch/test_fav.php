<?php

use GuzzleHttp\Client;

require __DIR__.'/../vendor/autoload.php';

$client = new Client(['verify' => false, 'timeout' => 15]);
$res = $client->get('https://lifeguarddatarecovery.com/wp-content/uploads/2022/03/fav.png');
echo 'fav.png status: '.$res->getStatusCode().' length: '.strlen($res->getBody()->getContents())."\n";

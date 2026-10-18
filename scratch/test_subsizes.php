<?php

use GuzzleHttp\Client;

require __DIR__.'/../vendor/autoload.php';

$client = new Client(['verify' => false]);
$urls = [
    'https://lifeguarddatarecovery.com/wp-content/uploads/2026/10/lifeguarddatarecovery-1-3.jpg',
    'https://lifeguarddatarecovery.com/wp-content/uploads/2026/10/lifeguarddatarecovery-1-3-300x174.jpg',
    'https://lifeguarddatarecovery.com/wp-content/uploads/2026/10/lifeguarddatarecovery-1-3-150x150.jpg',
];

foreach ($urls as $u) {
    try {
        $r = $client->head($u);
        echo $r->getStatusCode().' '.$u."\n";
    } catch (Throwable $e) {
        echo "ERR for {$u}: ".$e->getMessage()."\n";
    }
}

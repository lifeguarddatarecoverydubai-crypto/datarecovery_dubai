<?php

$url = 'https://lifeguarddatarecovery.com/wp-json/wp/v2/media?per_page=10&page=1';
$res = file_get_contents($url);
$media = json_decode($res, true);

foreach ($media as $m) {
    $src = $m['source_url'] ?? '';
    $ch = curl_init($src);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "ID: {$m['id']} | Code: $code | Source: $src\n";
}

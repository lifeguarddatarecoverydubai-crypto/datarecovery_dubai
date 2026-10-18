<?php

function getWpTotal($endpoint)
{
    $ch = curl_init("https://lifeguarddatarecovery.com/wp-json/wp/v2/{$endpoint}?per_page=1");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $headerSize);
    curl_close($ch);

    if (preg_match('/X-WP-Total:\s*(\d+)/i', $header, $m)) {
        return (int) $m[1];
    }

    return -1;
}

echo 'Total Posts on WP: '.getWpTotal('posts')."\n";
echo 'Total Pages on WP: '.getWpTotal('pages')."\n";
echo 'Total Media on WP: '.getWpTotal('media')."\n";
echo 'Total Categories on WP: '.getWpTotal('categories')."\n";
echo 'Total Tags on WP: '.getWpTotal('tags')."\n";

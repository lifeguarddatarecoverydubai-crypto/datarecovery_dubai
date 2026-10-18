<?php

$url = 'https://lifeguarddatarecovery.com/wp-json/wp/v2/posts?slug=can-data-be-recovered-from-an-ssd-with-corrupted-firmware';
$res = file_get_contents($url);
$p = json_decode($res, true)[0];
echo 'Date: '.$p['date']."\n";
echo 'Date GMT: '.$p['date_gmt']."\n";
echo 'Modified: '.$p['modified']."\n";
echo 'Status: '.$p['status']."\n";
echo 'Featured media ID: '.$p['featured_media']."\n";

// Fetch the media object directly:
$mediaUrl = 'https://lifeguarddatarecovery.com/wp-json/wp/v2/media/'.$p['featured_media'];
$mRes = @file_get_contents($mediaUrl);
if ($mRes) {
    $m = json_decode($mRes, true);
    echo 'Media Date: '.$m['date']."\n";
    echo 'Media Source: '.$m['source_url']."\n";
    echo 'Media Details File: '.($m['media_details']['file'] ?? '')."\n";
}

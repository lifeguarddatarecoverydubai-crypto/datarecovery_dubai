<?php

require __DIR__.'/../vendor/autoload.php';

$url = 'https://lifeguarddatarecovery.com/wp-content/uploads/2026/10/lifeguarddatarecovery-1-3.jpg';

// Test 1: with curl command line or PHP curl
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Referer: https://lifeguarddatarecovery.com/',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
]);
$data = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

echo "With Referer:\n";
echo 'HTTP Code: '.$code."\n";
echo 'Content-Type: '.$contentType."\n";
echo 'Length: '.strlen($data)."\n";

// Test 2: Try another image (e.g. 2026/09 or logo or elementor)
$url2 = 'https://lifeguarddatarecovery.com/wp-content/uploads/2024/08/logo.png';
$ch2 = curl_init($url2);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch2, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
$data2 = curl_exec($ch2);
$code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);
echo "\nLogo image (2024/08/logo.png):\n";
echo 'HTTP Code: '.$code2."\n";
echo 'Length: '.strlen($data2)."\n";

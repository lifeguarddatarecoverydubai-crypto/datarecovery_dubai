<?php

$url = 'https://lifeguarddatarecovery.com/wp-content/uploads/2026/10/lifeguarddatarecovery-1-3.jpg';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36');
$res = curl_exec($ch);
curl_close($ch);

echo substr($res, 0, 1000)."\n";

// Let's also test if other uploads like 2024 or 2025 or 2022 work:
$testUrls = [
    'https://lifeguarddatarecovery.com/wp-content/uploads/2022/03/hard-drive-recovery.jpg',
    'https://lifeguarddatarecovery.com/wp-content/uploads/2025/09/CCTV-Footage-Recovery-Dubai.jpg',
    'https://lifeguarddatarecovery.com/wp-content/uploads/2024/06/NAS-data-recovery-dubai-qmzj45gqc0ig4drmfzxzkzbtt0z237chd3x8wus56o.jpeg',
    'https://lifeguarddatarecovery.com/wp-content/uploads/2024/10/lifeguarddatarecovery-1-3.jpg',
    'https://lifeguarddatarecovery.com/wp-content/uploads/2025/10/lifeguarddatarecovery-1-3.jpg',
];

foreach ($testUrls as $u) {
    $c = curl_init($u);
    curl_setopt($c, CURLOPT_NOBODY, true);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_exec($c);
    $code = curl_getinfo($c, CURLINFO_HTTP_CODE);
    curl_close($c);
    echo "$code: $u\n";
}

<?php

$csv = 'c:\\Users\\Graphics\\Desktop\\internal_all.csv';
$file = fopen($csv, 'r');
$header = fgetcsv($file);

$serviceUrls = [];

while (($row = fgetcsv($file)) !== false) {
    $url = $row[0] ?? '';
    if (preg_match('#/(hard-drive|ssd|raid|nas|server|cctv|sd-card|iphone|mac|ransomware|data-recovery)[^/]*#', $url, $m)) {
        $serviceUrls[] = $url;
    }
}
fclose($file);

echo 'Found '.count($serviceUrls)." data recovery service URLs in CSV:\n";
print_r(array_unique($serviceUrls));

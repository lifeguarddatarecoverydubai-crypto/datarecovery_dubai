<?php

$csv = 'c:\\Users\\Graphics\\Desktop\\internal_all.csv';
$file = fopen($csv, 'r');
$header = fgetcsv($file);

// Find index of Status Code, Content Type, etc.
$statusIndex = array_search('Status Code', $header);
$contentTypeIndex = array_search('Content Type', $header);
$redirectUriIndex = array_search('Redirect URL', $header);

$non200 = [];
$htmlUrls = [];

while (($row = fgetcsv($file)) !== false) {
    $url = $row[0] ?? '';
    $status = $row[$statusIndex] ?? '';
    $type = $row[$contentTypeIndex] ?? '';

    if ($status !== '200') {
        $non200[] = [
            'url' => $url,
            'status' => $status,
            'type' => $type,
            'redirect' => $redirectUriIndex !== false ? ($row[$redirectUriIndex] ?? '') : '',
        ];
    }
    if (str_contains($type, 'text/html')) {
        $htmlUrls[] = $url;
    }
}
fclose($file);

echo 'Total Non-200 URLs: '.count($non200)."\n";
print_r($non200);

echo "\nChecking for date archive HTML URLs:\n";
foreach ($htmlUrls as $u) {
    if (preg_match('#/\d{4}/\d{2}#', $u)) {
        echo "Date archive: $u\n";
    }
}

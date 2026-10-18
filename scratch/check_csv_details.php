<?php

$csvPath = 'c:\\Users\\Graphics\\Desktop\\internal_all.csv';
$handle = fopen($csvPath, 'r');
$headers = fgetcsv($handle);
$headerMap = array_flip($headers);

echo "--- DETAILS ON 404 ERRORS IN CSV ---\n";
rewind($handle);
fgetcsv($handle); // skip header

while (($row = fgetcsv($handle)) !== false) {
    $url = $row[$headerMap['Address'] ?? 0] ?? '';
    $statusCode = $row[$headerMap['Status Code'] ?? 2] ?? '';
    $inlinks = $row[$headerMap['Inlinks'] ?? 0] ?? '0';
    $source = $row[$headerMap['Redirect URL'] ?? 0] ?? '';

    if (str_starts_with($statusCode, '4')) {
        echo "404 URL: {$url}\n";
        echo "  Inlinks count: {$inlinks}\n";
    }
}

echo "\n--- ALL 301 REDIRECTS IN CSV ---\n";
rewind($handle);
fgetcsv($handle);

while (($row = fgetcsv($handle)) !== false) {
    $url = $row[$headerMap['Address'] ?? 0] ?? '';
    $statusCode = $row[$headerMap['Status Code'] ?? 2] ?? '';
    $target = $row[$headerMap['Redirect URL'] ?? 62] ?? '';

    if (str_starts_with($statusCode, '3')) {
        echo "301: {$url} => {$target}\n";
    }
}

fclose($handle);

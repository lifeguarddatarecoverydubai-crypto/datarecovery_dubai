<?php

$csvPath = 'c:\\Users\\Graphics\\Desktop\\internal_all.csv';
$handle = fopen($csvPath, 'r');
$headers = fgetcsv($handle);
$headerMap = array_flip($headers);

$longTitles = [];
$shortTitles = [];
$longDescs = [];
$shortDescs = [];
$slowPages = [];

while (($row = fgetcsv($handle)) !== false) {
    $url = $row[$headerMap['Address'] ?? 0] ?? '';
    $statusCode = $row[$headerMap['Status Code'] ?? 2] ?? '';
    $contentType = explode(';', $row[$headerMap['Content Type'] ?? 1] ?? '')[0];
    $title = $row[$headerMap['Title 1'] ?? 6] ?? '';
    $titleLen = (int) ($row[$headerMap['Title 1 Length'] ?? 7] ?? 0);
    $desc = $row[$headerMap['Meta Description 1'] ?? 9] ?? '';
    $descLen = (int) ($row[$headerMap['Meta Description 1 Length'] ?? 10] ?? 0);
    $time = (float) ($row[$headerMap['Response Time'] ?? 60] ?? 0);

    if (str_contains($contentType, 'html') && $statusCode === '200') {
        if ($titleLen > 65) {
            $longTitles[] = ['url' => $url, 'title' => $title, 'len' => $titleLen];
        } elseif ($titleLen < 30 && $titleLen > 0) {
            $shortTitles[] = ['url' => $url, 'title' => $title, 'len' => $titleLen];
        }

        if ($descLen > 160) {
            $longDescs[] = ['url' => $url, 'desc' => $desc, 'len' => $descLen];
        } elseif ($descLen < 70 && $descLen > 0) {
            $shortDescs[] = ['url' => $url, 'desc' => $desc, 'len' => $descLen];
        }

        if ($time > 1.0) {
            $slowPages[] = ['url' => $url, 'time' => $time];
        }
    }
}
fclose($handle);

echo "--- TITLE & DESCRIPTION LENGTHS ---\n";
echo 'Titles > 65 chars (truncated in Google SERPs): '.count($longTitles)."\n";
foreach (array_slice($longTitles, 0, 5) as $t) {
    echo "  [{$t['len']} chars] {$t['url']}\n    \"{$t['title']}\"\n";
}

echo "\nTitles < 30 chars (under-optimized): ".count($shortTitles)."\n";
foreach (array_slice($shortTitles, 0, 5) as $t) {
    echo "  [{$t['len']} chars] {$t['url']}\n    \"{$t['title']}\"\n";
}

echo "\nDescriptions > 160 chars (truncated in SERPs): ".count($longDescs)."\n";
foreach (array_slice($longDescs, 0, 5) as $d) {
    echo "  [{$d['len']} chars] {$d['url']}\n";
}

echo "\nDescriptions < 70 chars (too short): ".count($shortDescs)."\n";
foreach (array_slice($shortDescs, 0, 5) as $d) {
    echo "  [{$d['len']} chars] {$d['url']}\n    \"{$d['desc']}\"\n";
}

echo "\nPages with response time > 1.0s on old WordPress server: ".count($slowPages)."\n";

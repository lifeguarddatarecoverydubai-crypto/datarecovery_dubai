<?php

$file = 'C:/Users/Graphics/Desktop/internal_all.csv';
$handle = fopen($file, 'r');
$headers = fgetcsv($handle);
$headerMap = array_flip($headers);

$urlCol = $headerMap['Address'] ?? 0;
$statusCol = $headerMap['Status Code'] ?? null;
$titleCol = $headerMap['Title 1'] ?? null;
$titleLengthCol = $headerMap['Title 1 Length'] ?? null;
$metaDescCol = $headerMap['Meta Description 1'] ?? null;
$metaDescLengthCol = $headerMap['Meta Description 1 Length'] ?? null;
$h1Col = $headerMap['H1-1'] ?? null;
$h1CountCol = $headerMap['H1-1 Length'] ?? null;
$canonicalCol = $headerMap['Canonical Link Element 1'] ?? null;

$titles = [];
$metaDescs = [];
$longTitles = [];
$shortTitles = [];
$longMeta = [];
$shortMeta = [];

while (($row = fgetcsv($handle)) !== false) {
    $url = $row[$urlCol] ?? '';
    $status = $row[$statusCol] ?? '';
    if ($status != '200') {
        continue;
    }
    if (preg_match('/\.(jpg|jpeg|png|webp|gif|svg|pdf|css|js|woff|woff2)/i', $url)) {
        continue;
    }

    $title = $row[$titleCol] ?? '';
    $meta = $row[$metaDescCol] ?? '';

    if ($title) {
        $titles[$title][] = $url;
        $tLen = mb_strlen($title);
        if ($tLen > 65) {
            $longTitles[] = ['url' => $url, 'title' => $title, 'len' => $tLen];
        }
        if ($tLen < 25) {
            $shortTitles[] = ['url' => $url, 'title' => $title, 'len' => $tLen];
        }
    }

    if ($meta) {
        $metaDescs[$meta][] = $url;
        $mLen = mb_strlen($meta);
        if ($mLen > 165) {
            $longMeta[] = ['url' => $url, 'meta' => $meta, 'len' => $mLen];
        }
        if ($mLen < 50) {
            $shortMeta[] = ['url' => $url, 'meta' => $meta, 'len' => $mLen];
        }
    }
}
fclose($handle);

echo "=== DUPLICATE TITLES ===\n";
$dupTitles = array_filter($titles, fn ($urls) => count($urls) > 1);
echo 'Duplicate title count: '.count($dupTitles)."\n";
foreach (array_slice($dupTitles, 0, 5) as $t => $urls) {
    echo "Title: $t (".count($urls)." pages)\n";
    foreach (array_slice($urls, 0, 3) as $u) {
        echo "  - $u\n";
    }
}

echo "\n=== DUPLICATE META DESCRIPTIONS ===\n";
$dupMetas = array_filter($metaDescs, fn ($urls) => count($urls) > 1);
echo 'Duplicate meta desc count: '.count($dupMetas)."\n";
foreach (array_slice($dupMetas, 0, 5) as $m => $urls) {
    echo 'Meta: '.substr($m, 0, 60).'... ('.count($urls)." pages)\n";
    foreach (array_slice($urls, 0, 3) as $u) {
        echo "  - $u\n";
    }
}

echo "\n=== TITLE LENGTHS ===\n";
echo 'Long titles (>65 chars): '.count($longTitles)."\n";
echo 'Short titles (<25 chars): '.count($shortTitles)."\n";

echo "\n=== META DESCRIPTION LENGTHS ===\n";
echo 'Long meta descriptions (>165 chars): '.count($longMeta)."\n";
echo 'Short meta descriptions (<50 chars): '.count($shortMeta)."\n";

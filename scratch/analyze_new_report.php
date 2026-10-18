<?php

$file = 'C:/Users/Graphics/Desktop/internal_html.csv';
if (! file_exists($file)) {
    echo "File not found: $file\n";
    exit(1);
}

$handle = fopen($file, 'r');
$headers = fgetcsv($handle);
$headerMap = array_flip($headers);

$urlCol = $headerMap['Address'] ?? $headerMap['URL'] ?? 0;
$statusCol = $headerMap['Status Code'] ?? null;
$statusMsgCol = $headerMap['Status'] ?? null;
$indexableCol = $headerMap['Indexability'] ?? null;
$indexableStatusCol = $headerMap['Indexability Status'] ?? null;
$titleCol = $headerMap['Title 1'] ?? $headerMap['Title'] ?? null;
$metaDescCol = $headerMap['Meta Description 1'] ?? $headerMap['Meta Description'] ?? null;
$h1Col = $headerMap['H1-1'] ?? $headerMap['H1'] ?? null;
$canonicalCol = $headerMap['Canonical Link Element 1'] ?? null;
$redirectUrlCol = $headerMap['Redirect URL'] ?? null;

$statusCodes = [];
$indexabilityCounts = [];
$non200 = [];
$canonicalMismatches = [];
$missingMeta = [];
$missingTitles = [];
$missingH1 = [];
$totalRows = 0;

while (($row = fgetcsv($handle)) !== false) {
    $totalRows++;
    $url = $row[$urlCol] ?? '';
    $status = $statusCol !== null ? ($row[$statusCol] ?? '') : '';
    $statusMsg = $statusMsgCol !== null ? ($row[$statusMsgCol] ?? '') : '';
    $indexable = $indexableCol !== null ? ($row[$indexableCol] ?? '') : '';
    $indexableStatus = $indexableStatusCol !== null ? ($row[$indexableStatusCol] ?? '') : '';
    $title = $titleCol !== null ? ($row[$titleCol] ?? '') : '';
    $metaDesc = $metaDescCol !== null ? ($row[$metaDescCol] ?? '') : '';
    $h1 = $h1Col !== null ? ($row[$h1Col] ?? '') : '';
    $canonical = $canonicalCol !== null ? ($row[$canonicalCol] ?? '') : '';
    $redirectUrl = $redirectUrlCol !== null ? ($row[$redirectUrlCol] ?? '') : '';

    $statusCodes[$status] = ($statusCodes[$status] ?? 0) + 1;
    $indexabilityCounts[$indexable] = ($indexabilityCounts[$indexable] ?? 0) + 1;

    if ($status != '200') {
        $non200[] = [
            'url' => $url,
            'status' => $status,
            'msg' => $statusMsg,
            'redirect' => $redirectUrl,
            'indexable' => $indexable,
            'reason' => $indexableStatus,
        ];
    }

    if ($status == '200' && $canonical && $canonical !== $url) {
        $canonicalMismatches[] = [
            'url' => $url,
            'canonical' => $canonical,
        ];
    }

    if ($status == '200' && empty($metaDesc)) {
        $missingMeta[] = $url;
    }

    if ($status == '200' && empty($title)) {
        $missingTitles[] = $url;
    }

    if ($status == '200' && empty($h1)) {
        $missingH1[] = $url;
    }

    if (str_contains($url, '50-kva')) {
        echo "Found 50-kva URL: $url | Meta Desc: '$metaDesc' | Canonical: '$canonical'\n";
    }
}

fclose($handle);

echo "\n============================================\n";
echo "SCREAMING FROG NEW REPORT ANALYSIS\n";
echo "============================================\n";
echo "Total HTML URLs Crawled: $totalRows\n\n";

echo "--- Status Code Breakdown ---\n";
print_r($statusCodes);

echo "\n--- Indexability Breakdown ---\n";
print_r($indexabilityCounts);

echo "\n--- Non-200 URLs (".count($non200).") ---\n";
foreach ($non200 as $item) {
    echo "[{$item['status']}] {$item['url']}";
    if ($item['redirect']) {
        echo " -> {$item['redirect']}";
    }
    if ($item['reason']) {
        echo " ({$item['reason']})";
    }
    echo "\n";
}

echo "\n--- Canonical Mismatches on 200 OK (".count($canonicalMismatches).") ---\n";
foreach ($canonicalMismatches as $item) {
    echo "URL: {$item['url']} \nCanonical: {$item['canonical']}\n\n";
}

echo "\n--- Missing Meta Descriptions (".count($missingMeta).") ---\n";
foreach ($missingMeta as $u) {
    echo " - $u\n";
}

echo "\n--- Missing Titles (".count($missingTitles).") ---\n";
foreach ($missingTitles as $u) {
    echo " - $u\n";
}

echo "\n--- Missing H1 (".count($missingH1).") ---\n";
foreach ($missingH1 as $u) {
    echo " - $u\n";
}

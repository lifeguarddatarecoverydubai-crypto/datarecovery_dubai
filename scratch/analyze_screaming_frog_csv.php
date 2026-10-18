<?php

$csvPath = 'c:\\Users\\Graphics\\Desktop\\internal_all.csv';

if (! file_exists($csvPath)) {
    exit("File not found: {$csvPath}\n");
}

$handle = fopen($csvPath, 'r');
if (! $handle) {
    exit("Unable to open CSV.\n");
}

$headers = fgetcsv($handle);
$headerMap = array_flip($headers);

$totalRows = 0;
$statusCodes = [];
$contentTypes = [];
$indexability = [];
$errors4xx = [];
$redirects3xx = [];
$missingTitle = [];
$missingDesc = [];
$missingH1 = [];
$multipleH1 = [];
$missingCanon = [];
$canonMismatch = [];
$noindexUrls = [];

while (($row = fgetcsv($handle)) !== false) {
    $totalRows++;

    $url = $row[$headerMap['Address'] ?? 0] ?? '';
    $statusCode = $row[$headerMap['Status Code'] ?? 2] ?? '';
    $contentType = explode(';', $row[$headerMap['Content Type'] ?? 1] ?? '')[0];
    $idx = $row[$headerMap['Indexability'] ?? 4] ?? '';
    $idxStatus = $row[$headerMap['Indexability Status'] ?? 5] ?? '';
    $title = $row[$headerMap['Title 1'] ?? 6] ?? '';
    $desc = $row[$headerMap['Meta Description 1'] ?? 9] ?? '';
    $h1_1 = $row[$headerMap['H1-1'] ?? 14] ?? '';
    $h1_2 = $row[$headerMap['H1-2'] ?? 16] ?? '';
    $canonical = $row[$headerMap['Canonical Link Element 1'] ?? 25] ?? '';
    $redirectUrl = $row[$headerMap['Redirect URL'] ?? 62] ?? '';

    $statusCodes[$statusCode] = ($statusCodes[$statusCode] ?? 0) + 1;
    $contentTypes[$contentType] = ($contentTypes[$contentType] ?? 0) + 1;
    $indexability[$idx.' ('.$idxStatus.')'] = ($indexability[$idx.' ('.$idxStatus.')'] ?? 0) + 1;

    // 4xx errors
    if (str_starts_with($statusCode, '4')) {
        $errors4xx[] = [
            'url' => $url,
            'code' => $statusCode,
            'type' => $contentType,
        ];
    }

    // 3xx redirects
    if (str_starts_with($statusCode, '3')) {
        $redirects3xx[] = [
            'url' => $url,
            'target' => $redirectUrl,
            'code' => $statusCode,
        ];
    }

    // For HTML pages:
    if (str_contains($contentType, 'html') && $statusCode === '200') {
        if (empty(trim($title))) {
            $missingTitle[] = $url;
        }
        if (empty(trim($desc))) {
            $missingDesc[] = $url;
        }
        if (empty(trim($h1_1))) {
            $missingH1[] = $url;
        }
        if (! empty(trim($h1_2))) {
            $multipleH1[] = ['url' => $url, 'h1_1' => $h1_1, 'h1_2' => $h1_2];
        }
        if (empty(trim($canonical))) {
            $missingCanon[] = $url;
        } elseif (rtrim($canonical, '/') !== rtrim($url, '/')) {
            $canonMismatch[] = ['url' => $url, 'canonical' => $canonical];
        }
        if ($idx === 'Non-Indexable') {
            $noindexUrls[] = ['url' => $url, 'reason' => $idxStatus];
        }
    }
}
fclose($handle);

echo "=======================================================\n";
echo "  SCREAMING FROG SEO AUDIT ANALYSIS (internal_all.csv)\n";
echo "=======================================================\n";
echo "Total Rows Crawled: {$totalRows}\n\n";

echo "--- 1. STATUS CODES BREAKDOWN ---\n";
arsort($statusCodes);
foreach ($statusCodes as $code => $count) {
    echo "  HTTP {$code}: {$count}\n";
}

echo "\n--- 2. CONTENT TYPES BREAKDOWN ---\n";
arsort($contentTypes);
foreach ($contentTypes as $type => $count) {
    echo "  {$type}: {$count}\n";
}

echo "\n--- 3. INDEXABILITY BREAKDOWN ---\n";
arsort($indexability);
foreach ($indexability as $idx => $count) {
    echo "  {$idx}: {$count}\n";
}

echo "\n--- 4. 4XX CLIENT ERRORS (COUNT: ".count($errors4xx).") ---\n";
foreach ($errors4xx as $err) {
    echo "  [{$err['code']}] {$err['url']} ({$err['type']})\n";
}

echo "\n--- 5. 3XX REDIRECTS (COUNT: ".count($redirects3xx).") ---\n";
foreach (array_slice($redirects3xx, 0, 15) as $r) {
    echo "  [{$r['code']}] {$r['url']} => {$r['target']}\n";
}
if (count($redirects3xx) > 15) {
    echo '  ... and '.(count($redirects3xx) - 15)." more redirects.\n";
}

echo "\n--- 6. ON-PAGE SEO ISSUES (200 OK HTML PAGES) ---\n";
echo '  Missing Title: '.count($missingTitle)."\n";
foreach ($missingTitle as $u) {
    echo "    - {$u}\n";
}

echo '  Missing Meta Description: '.count($missingDesc)."\n";
foreach ($missingDesc as $u) {
    echo "    - {$u}\n";
}

echo '  Missing H1: '.count($missingH1)."\n";
foreach ($missingH1 as $u) {
    echo "    - {$u}\n";
}

echo '  Multiple H1s: '.count($multipleH1)."\n";
foreach (array_slice($multipleH1, 0, 5) as $m) {
    echo "    - {$m['url']}\n      H1-1: {$m['h1_1']}\n      H1-2: {$m['h1_2']}\n";
}

echo '  Missing Canonical: '.count($missingCanon)."\n";
foreach ($missingCanon as $u) {
    echo "    - {$u}\n";
}

echo '  Canonical Mismatch: '.count($canonMismatch)."\n";
foreach ($canonMismatch as $cm) {
    echo "    - {$cm['url']} => Canonical: {$cm['canonical']}\n";
}

echo '  Non-Indexable 200 Pages: '.count($noindexUrls)."\n";
foreach ($noindexUrls as $ni) {
    echo "    - {$ni['url']} (Reason: {$ni['reason']})\n";
}

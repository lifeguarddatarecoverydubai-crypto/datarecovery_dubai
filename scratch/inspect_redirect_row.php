<?php

$file = 'C:/Users/Graphics/Desktop/internal_all.csv';
$handle = fopen($file, 'r');
$headers = fgetcsv($handle);

while (($row = fgetcsv($handle)) !== false) {
    $url = $row[0] ?? '';
    if (str_contains($url, '/services/power-plant') || str_contains($url, '/services/amc-generator') || str_contains($url, '/services/portacabin')) {
        echo "URL: $url\n";
        foreach ($headers as $idx => $h) {
            $val = $row[$idx] ?? '';
            if (! empty($val)) {
                echo "  $h: $val\n";
            }
        }
        echo "--------------------------\n";
    }
}
fclose($handle);

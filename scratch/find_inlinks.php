<?php

$file = 'C:/Users/Graphics/Desktop/internal_all.csv';
$lines = file($file);

foreach ($lines as $lineNum => $line) {
    if (str_contains($line, 'services/power-plant') || str_contains($line, 'services/amc-generator') || str_contains($line, 'services/portacabin')) {
        echo "Line $lineNum:\n";
        echo substr($line, 0, 300)."...\n\n";
    }
}

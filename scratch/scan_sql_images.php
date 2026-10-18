<?php

$file = fopen(__DIR__.'/../database.sql', 'r');
$count = 0;
while (($line = fgets($file)) !== false) {
    if (strpos($line, 'INSERT INTO `posts`') !== false || strpos($line, 'wp-content/uploads') !== false) {
        if (preg_match_all('/https?:\/\/[^"\',;\s]+/i', $line, $urls)) {
            foreach ($urls[0] as $u) {
                if (strpos($u, 'uploads') !== false) {
                    echo "Found in SQL: $u\n";
                    $count++;
                    if ($count > 10) {
                        break 2;
                    }
                }
            }
        }
    }
}
fclose($file);
echo "Total samples: $count\n";

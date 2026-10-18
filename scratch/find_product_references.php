<?php

$dir = 'd:/CMS/lifeguard_dubai';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$matches = [];

foreach ($iterator as $file) {
    if ($file->isDir()) {
        continue;
    }
    $path = str_replace('\\', '/', $file->getPathname());
    if (str_contains($path, '/vendor/') || str_contains($path, '/storage/') || str_contains($path, '/.git/') || str_contains($path, '/node_modules/')) {
        continue;
    }
    if (preg_match('/\.(php|blade\.php)$/', $path)) {
        $content = file_get_contents($path);
        if (preg_match_all('/(fleetProduct|Product::|products|generator|power-plant|rental-power|amc-generator|new-generators)/i', $content, $m)) {
            $matches[$path] = array_unique($m[0]);
        }
    }
}

foreach ($matches as $path => $found) {
    echo "$path => ".implode(', ', $found)."\n";
}

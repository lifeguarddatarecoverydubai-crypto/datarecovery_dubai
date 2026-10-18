<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$request = Request::create('/', 'GET');
$response = $app->handle($request);
$html = $response->getContent();

// Extract the desktop header <nav>
preg_match('/<!-- Desktop Nav Links -->(.*?)<\/nav>/s', $html, $matches);
$navHtml = $matches[1] ?? '';

echo "=== DESKTOP NAV HTML ===\n";
echo $navHtml."\n";

// Count occurrences of links
$homeCount = substr_count($navHtml, '>Home<');
$aboutCount = substr_count($navHtml, '>About Us<');
$servicesCount = substr_count($navHtml, 'Services');
$locationsCount = substr_count($navHtml, 'Locations');
$contactCount = substr_count($navHtml, '>Contact Us<');

echo "\n=== COUNTS IN DESKTOP NAV ===\n";
echo "Home occurrences: $homeCount\n";
echo "About Us occurrences: $aboutCount\n";
echo "Contact Us occurrences: $contactCount\n";

if ($homeCount === 1 && $aboutCount === 1 && $contactCount === 1) {
    echo "\nSUCCESS: Exactly 1 clean menu rendered in desktop nav!\n";
} else {
    echo "\nERROR: Duplicate menu items detected!\n";
}

<?php

use App\Http\Controllers\Frontend\SitemapController;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$ctrl = app(SitemapController::class);

$allXml = [
    'pages' => $ctrl->pages()->getContent(),
    'posts' => $ctrl->posts()->getContent(),
    'services' => $ctrl->services()->getContent(),
];

$errors = [];
$totalImages = 0;
foreach ($allXml as $type => $xml) {
    preg_match_all("/<image:loc>(.*?)<\/image:loc>/", $xml, $matches);
    foreach ($matches[1] as $loc) {
        $totalImages++;
        if (str_contains($loc, '/storage/storage') || str_contains($loc, '//storage') || ! filter_var($loc, FILTER_VALIDATE_URL)) {
            $errors[] = "[$type] Bad image URL: ".$loc;
        }
    }
}

echo "Total images checked across all sitemaps: $totalImages\n";
if (empty($errors)) {
    echo "SUCCESS: Zero broken image URLs or /storage/storage in sitemaps!\n";
} else {
    echo "ERRORS FOUND:\n".implode("\n", $errors)."\n";
}

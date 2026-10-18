<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Media;
use App\Models\Page;
use Illuminate\Contracts\Console\Kernel;

$media = Media::where('file_name', 'like', '%cctv%')->orWhere('file_path', 'like', '%cctv%')->get();
echo 'Found '.$media->count()." media items matching 'cctv':\n";
foreach ($media as $m) {
    echo "ID: {$m->id} | Name: {$m->file_name} | URL: {$m->url}\n";
}

// Also check live WordPress API for page or post with cctv
echo "\nChecking live WP API for cctv pages...\n";
$json = @file_get_contents('https://lifeguarddatarecovery.com/wp-json/wp/v2/pages?search=cctv&_embed=1');
if ($json) {
    $pages = json_decode($json, true);
    foreach ($pages as $p) {
        echo "Slug: {$p['slug']} | Title: {$p['title']['rendered']}\n";
        if (! empty($p['_embedded']['wp:featuredmedia'][0]['source_url'])) {
            echo '  Featured: '.$p['_embedded']['wp:featuredmedia'][0]['source_url']."\n";
        }
    }
}

<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;

$services = Service::all();
echo "=== 10 DATA RECOVERY SERVICES VERIFICATION ===\n";
foreach ($services as $s) {
    echo "Slug: {$s->slug}\n";
    echo "  Title: {$s->title}\n";
    echo "  Featured Image: {$s->featured_image}\n";
    echo "  Featured Image URL: {$s->featured_image_url}\n";
    echo '  Image Exists on Disk: '.(file_exists(public_path($s->featured_image)) ? 'YES' : 'NO')."\n";
    echo '  Content Length: '.strlen($s->content ?? '')."\n";
    preg_match_all('/<h[1-4][^>]*>(.*?)<\/h[1-4]>/i', $s->content ?? '', $matches);
    echo '  Headings: '.count($matches[0])."\n";
    foreach (array_slice($matches[0], 0, 3) as $h) {
        echo '    '.strip_tags($h)."\n";
    }
    echo "----------------------------------------\n";
}

<?php

use App\Models\Page;
use Illuminate\Contracts\Console\Kernel;

$ch = curl_init('https://lifeguarddatarecovery.com/career/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$html = curl_exec($ch);
curl_close($ch);

echo 'Careers page length: '.strlen($html)."\n";
echo 'Contains data-recovery-engineer: '.(stripos($html, 'data-recovery-engineer') !== false ? 'YES' : 'NO')."\n";
echo 'Contains sales-executive: '.(stripos($html, 'sales-executive') !== false ? 'YES' : 'NO')."\n";
echo 'Contains social-media-marketing: '.(stripos($html, 'social-media-marketing') !== false ? 'YES' : 'NO')."\n";

// Let's see what is inside those 3 pages locally
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

foreach (['data-recovery-engineer', 'sales-executive', 'social-media-marketing'] as $s) {
    $p = Page::where('slug', $s)->first();
    echo "Slug: {$s} | Title: {$p->title} | Status: {$p->status} | Indexable: ".($p->is_indexable ? 'YES' : 'NO')."\n";
}

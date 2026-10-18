<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;

echo "=== HOMEPAGE SERVICES PREVIEW ===\n";
$services = Service::published()->ordered()->get();
echo 'Count: '.$services->count()."\n";
foreach ($services as $i => $s) {
    echo ($i + 1).'. '.$s->title.' ('.$s->slug.') [Icon: '.$s->icon."]\n";
}

$hasGenerator = false;
foreach ($services as $s) {
    if (preg_match('/generator|power plant|kva|portacabin/i', $s->title.' '.$s->slug.' '.$s->summary)) {
        $hasGenerator = true;
        echo "WARNING: Generator found in {$s->title}!\n";
    }
}
if (! $hasGenerator) {
    echo "\n>>> VERIFIED: ZERO GENERATOR REFERENCES FOUND IN ANY SERVICE! <<<\n";
}

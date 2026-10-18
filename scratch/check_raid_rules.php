<?php

use App\Models\RedirectRule;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$rules = RedirectRule::where('source_url', 'like', '%raid-data-recovery%')->get();
echo 'Rules found: '.$rules->count()."\n";
foreach ($rules as $r) {
    echo "- Source: {$r->source_url} => Target: {$r->target_url} ({$r->status_code})\n";
}

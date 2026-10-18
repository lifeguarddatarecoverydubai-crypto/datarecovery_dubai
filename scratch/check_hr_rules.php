<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\RedirectRule;
use Illuminate\Contracts\Console\Kernel;

$rules = RedirectRule::where('source_url', 'like', '%hard-drive%')->get();
foreach ($rules as $r) {
    echo "ID: {$r->id} | {$r->source_url} => {$r->target_url}\n";
}

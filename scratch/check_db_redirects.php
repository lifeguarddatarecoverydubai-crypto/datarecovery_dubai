<?php

use App\Models\RedirectRule;
use Illuminate\Contracts\Http\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo 'Total Redirect Rules in DB: '.RedirectRule::count()."\n";
foreach (RedirectRule::take(25)->get() as $r) {
    echo "  {$r->source_url} => {$r->target_url} ({$r->status_code})\n";
}

<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$tables = ['posts', 'pages', 'services', 'categories', 'tags'];
foreach ($tables as $t) {
    $indexes = DB::select('SHOW INDEXES FROM '.$t);
    $slugIndexed = false;
    foreach ($indexes as $idx) {
        if ($idx->Column_name === 'slug') {
            $slugIndexed = true;
            break;
        }
    }
    echo "$t slug indexed: ".($slugIndexed ? 'YES' : 'NO')."\n";
}

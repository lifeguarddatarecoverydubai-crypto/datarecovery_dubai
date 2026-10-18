<?php

use App\Models\Page;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$pages = Page::where('schema_markup', 'like', '%muskan%')
    ->orWhere('schema_markup', 'like', '%shafia%')
    ->orWhere('schema_markup', 'like', '%Person%')
    ->count();
echo "Pages with personal author or muskan/shafia in schema: {$pages}\n";

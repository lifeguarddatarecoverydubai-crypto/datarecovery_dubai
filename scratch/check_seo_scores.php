<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;

echo 'Pages with seo_score == 0 or null: '.Page::where('seo_score', 0)->orWhereNull('seo_score')->count()."\n";
echo 'Services with seo_score == 0 or null: '.Service::where('seo_score', 0)->orWhereNull('seo_score')->count()."\n";

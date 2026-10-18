<?php

use App\Models\Page;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$p147 = Page::find(147);
$p147->seo_score = null;
$p147->save();
echo 'Locations new score: '.$p147->seo_score."\n";

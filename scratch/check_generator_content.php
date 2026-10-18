<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;

$dataRecoveryServices = Service::whereIn('id', [24, 25, 26, 27, 28, 29, 30, 31, 32, 33])->get();
foreach ($dataRecoveryServices as $s) {
    echo "ID: {$s->id} | Slug: {$s->slug}\n";
    echo "Title: {$s->title}\n";
    echo "Icon: {$s->icon}\n";
    echo 'Summary: '.substr($s->summary, 0, 100)."...\n";
    echo 'Features count: '.count($s->features ?? [])."\n";
    echo 'Content length: '.strlen($s->content ?? '')."\n";
    echo "----------------------------------------\n";
}

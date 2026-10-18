<?php

use App\Models\Category;
use Illuminate\Contracts\Http\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$cats = Category::all();
echo 'Total categories: '.$cats->count()."\n";
foreach ($cats as $c) {
    echo "  - {$c->name} ({$c->slug}) => Description: ".($c->description ?: '[EMPTY - Needs description!]')."\n";
}

<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

foreach (User::all() as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Slug: {$u->slug} | Email: {$u->email} | Posts: {$u->posts()->count()}\n";
}

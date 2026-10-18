<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Media;
use Illuminate\Contracts\Console\Kernel;

$lgMedia = Media::where('file_name', 'like', '%lifeguarddatarecovery%')
    ->orWhere('file_path', 'like', '%lifeguarddatarecovery%')
    ->limit(20)
    ->get();

foreach ($lgMedia as $m) {
    $existsStorage = file_exists(storage_path('app/public/'.$m->file_path));
    $existsPublic = file_exists(public_path('storage/'.$m->file_path));
    echo "ID: {$m->id} | Name: {$m->file_name} | Path: {$m->file_path} | StorageExists: ".($existsStorage ? 'YES' : 'NO').' | PublicExists: '.($existsPublic ? 'YES' : 'NO')."\n";
}

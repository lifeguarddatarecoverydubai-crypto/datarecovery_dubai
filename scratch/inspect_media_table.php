<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Media;
use Illuminate\Contracts\Console\Kernel;

$count = Media::count();
echo "Total Media records in DB: $count\n";

$samples = Media::limit(15)->get();
foreach ($samples as $m) {
    echo "ID: {$m->id} | Name: {$m->file_name} | Path: {$m->file_path}\n";
}

// Check how many have lifeguarddatarecovery- in file_name or file_path
$lgCount = Media::where('file_name', 'like', '%lifeguarddatarecovery%')->orWhere('file_path', 'like', '%lifeguarddatarecovery%')->count();
echo "Media matching 'lifeguarddatarecovery': $lgCount\n";

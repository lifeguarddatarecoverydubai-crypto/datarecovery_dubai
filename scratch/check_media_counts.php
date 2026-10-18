<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Media;
use Illuminate\Contracts\Console\Kernel;

$remoteCount = Media::where('file_path', 'like', 'http%')->count();
$localCount = Media::where('file_path', 'not like', 'http%')->count();

echo "Media with remote URLs (wp-content): $remoteCount\n";
echo "Media with local paths: $localCount\n";

$minMax = Media::selectRaw('MIN(id) as min_id, MAX(id) as max_id')->first();
echo "ID range: {$minMax->min_id} - {$minMax->max_id}\n";

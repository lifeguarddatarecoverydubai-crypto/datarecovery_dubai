<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\RedirectRule;
use App\Services\LocationService;
use Illuminate\Contracts\Console\Kernel;

echo 'Page data-recovery-abu-dhabi-2 exists: '.(Page::where('slug', 'data-recovery-abu-dhabi-2')->exists() ? 'YES' : 'NO')."\n";
echo 'Page data-recovery-abu-dhabi exists: '.(Page::where('slug', 'data-recovery-abu-dhabi')->exists() ? 'YES' : 'NO')."\n";
echo 'LocationService data-recovery-abu-dhabi is location: '.(LocationService::isLocationSlug('data-recovery-abu-dhabi') ? 'YES' : 'NO')."\n";
echo 'RedirectRule for data-recovery-abu-dhabi-2: '.RedirectRule::where('source_url', 'like', '%data-recovery-abu-dhabi-2%')->count()."\n";

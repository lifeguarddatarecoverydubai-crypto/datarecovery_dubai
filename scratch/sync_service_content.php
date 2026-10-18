<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\Service;
use Database\Seeders\DataRecoveryServicesContentSeeder;
use Illuminate\Contracts\Console\Kernel;

// 1. Slugs to map from DataRecoveryServicesContentSeeder
$seederFile = 'd:/CMS/lifeguard_dubai/database/seeders/DataRecoveryServicesContentSeeder.php';

// Let's create an instance of the seeder or read the array
// We can use reflection or extract data
$seederClass = new DataRecoveryServicesContentSeeder;

// Let's see: what if we read the array from the seeder file directly
$content = file_get_contents($seederFile);

// Let's match each service block
$serviceMap = [
    'hard-drive-recovery' => 'hard-drive-data-recovery-dubai',
    'ssd-recovery-dubai' => 'ssd-drive-data-recovery-in-dubai',
    'raid-recovery-dubai' => 'raid-data-recovery-service-dubai',
    'nas-recovery-dubai' => 'nas-data-recovery-dubai',
    'cctv-footage-recovery-dubai' => 'cctv-footage-recovery-dubai',
    'usb-data-recovery-dubai' => 'sd-card-data-recovery',
    'iphone-data-recovery-dubai' => 'iphone-data-recovery',
    'ransomware-data-recovery-dubai' => 'ransomware-data-recovery',
];

// If content in Service is empty, let's also check if Page has content
$services = Service::all();
foreach ($services as $s) {
    if (empty($s->content) || strlen($s->content) < 50) {
        $p = Page::where('slug', $s->slug)->first();
        if ($p && strlen($p->content) > 100) {
            $s->update(['content' => $p->content]);
            echo "Populated content for {$s->slug} from Page ({$p->title}, length: ".strlen($p->content).")\n";
        }
    }
}

echo "\n--- CURRENT SERVICES CONTENT STATUS ---\n";
foreach (Service::orderBy('order')->get() as $s) {
    echo "ID: {$s->id} | Order: {$s->order} | Slug: {$s->slug} | Content length: ".strlen($s->content ?? '').' | Features: '.count($s->features ?? [])."\n";
}

<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\Service;
use App\Services\WebpConverterService;
use Illuminate\Contracts\Console\Kernel;

$serviceImages = [
    'hard-drive-data-recovery-dubai' => 'https://lifeguarddatarecovery.com/wp-content/uploads/2022/03/hard-drive-recovery.jpg',
    'ssd-drive-data-recovery-in-dubai' => 'https://lifeguarddatarecovery.com/wp-content/uploads/2023/10/SSD-Data-Recovery-Dubai.png',
    'raid-data-recovery-service-dubai' => 'https://lifeguarddatarecovery.com/wp-content/uploads/2022/03/raid-recovery.jpg',
    'nas-data-recovery-dubai' => 'https://lifeguarddatarecovery.com/wp-content/uploads/2025/01/TS-412___Hardware_Specs-removebg-preview.png',
    'server-data-recovery-dubai' => 'https://lifeguarddatarecovery.com/wp-content/uploads/2024/12/raid-data-recovery-dubai.webp',
    'cctv-footage-recovery-dubai' => 'https://lifeguarddatarecovery.com/wp-content/uploads/2025/09/CCTV-Footage-Recovery-Dubai.jpg',
    'sd-card-data-recovery' => 'https://lifeguarddatarecovery.com/wp-content/uploads/2025/01/Untitled-design-16.webp',
    'iphone-data-recovery' => 'https://lifeguarddatarecovery.com/wp-content/uploads/2022/07/iphone-data-recovery.jpg',
    'mac-data-recovery' => 'https://lifeguarddatarecovery.com/wp-content/uploads/2022/03/mac-data-recovery.jpg',
    'ransomware-data-recovery' => 'https://lifeguarddatarecovery.com/wp-content/uploads/2022/06/ransomware.jpg',
];

$converter = app(WebpConverterService::class);

echo "=== SYNCHRONIZING AUTHENTIC SERVICE FEATURED IMAGES ===\n";

foreach ($serviceImages as $slug => $url) {
    echo "Processing $slug...\n";
    $service = Service::where('slug', $slug)->first();
    if (! $service) {
        echo "  Service not found!\n";

        continue;
    }

    $result = $converter->convertUrl($url, 'media/services', 85, 1600);
    if ($result && ! empty($result['file_path'])) {
        $storedPath = 'storage/'.$result['file_path'];
        $service->featured_image = $storedPath;
        $service->og_image = $result['url'];
        $service->save();
        echo "  [SUCCESS] Converted & Saved: {$storedPath} ({$result['dimensions']}, {$result['size']} bytes)\n";

        $page = Page::where('slug', $slug)->first();
        if ($page) {
            $page->featured_image = $storedPath;
            $page->og_image = $result['url'];
            $page->save();
        }
    } else {
        echo "  [FAILED] Could not convert URL: $url\n";
    }
}

echo "=== DONE ===\n";

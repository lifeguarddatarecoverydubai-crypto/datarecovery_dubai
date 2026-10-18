<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;

$lines = file('d:/CMS/lifeguard_dubai/database/seeders/DataRecoveryServicesContentSeeder.php');
$capturing = false;
$cctvContent = '';

foreach ($lines as $line) {
    if (str_contains($line, "'slug' => 'cctv-footage-recovery-dubai'")) {
        $capturing = true;
    }
    if ($capturing) {
        if (str_contains($line, "'content' => '")) {
            $cctvContent .= substr($line, strpos($line, "'content' => '") + 14);
        } elseif ($cctvContent !== '') {
            if (str_contains($line, "'],")) {
                $cctvContent .= substr($line, 0, strpos($line, "'],"));
                break;
            } else {
                $cctvContent .= $line;
            }
        }
    }
}

if ($cctvContent) {
    $service = Service::where('slug', 'cctv-footage-recovery-dubai')->first();
    if ($service) {
        $service->update(['content' => trim($cctvContent)]);
        echo 'Updated CCTV service content! Length: '.strlen($service->content)."\n";
    }
}

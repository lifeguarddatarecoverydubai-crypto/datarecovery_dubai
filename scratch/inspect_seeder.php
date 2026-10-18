<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$content = file_get_contents('d:/CMS/lifeguard_dubai/database/seeders/DataRecoveryServicesContentSeeder.php');
preg_match_all("/'title'\s*=>\s*'([^']+)'/", $content, $titles);
preg_match_all("/'slug'\s*=>\s*'([^']+)'/", $content, $slugs);

echo "Titles in Seeder:\n";
print_r($titles[1]);
echo "Slugs in Seeder:\n";
print_r($slugs[1]);

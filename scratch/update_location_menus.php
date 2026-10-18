<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

$locMenu = DB::table('menu_items')->where('title', 'like', '%Location%')->whereNull('parent_id')->first();
if ($locMenu) {
    echo "Found Locations menu ID: {$locMenu->id}\n";
    // Update parent URL to /locations
    DB::table('menu_items')->where('id', $locMenu->id)->update(['url' => '/locations']);

    // Remove old children
    DB::table('menu_items')->where('parent_id', $locMenu->id)->delete();

    // Insert clean verified branch children
    $children = [
        ['title' => 'All Locations Hub', 'url' => '/locations', 'order' => 1],
        ['title' => 'Dubai (Main Office - Bur Dubai)', 'url' => '/data-recovery-dubai', 'order' => 2],
        ['title' => 'Al Barsha, Dubai', 'url' => '/data-recovery-al-barsha', 'order' => 3],
        ['title' => 'Abu Dhabi', 'url' => '/data-recovery-abu-dhabi', 'order' => 4],
        ['title' => 'Sharjah', 'url' => '/data-recovery-sharjah', 'order' => 5],
        ['title' => 'Al Wasl, Dubai (SZR)', 'url' => '/data-recovery-al-wasl', 'order' => 6],
        ['title' => 'India (Secunderabad)', 'url' => '/data-recovery-india', 'order' => 7],
        ['title' => 'Kenya (Nairobi)', 'url' => '/data-recovery-service-in-kenya', 'order' => 8],
    ];

    foreach ($children as $child) {
        DB::table('menu_items')->insert([
            'parent_id' => $locMenu->id,
            'title' => $child['title'],
            'url' => $child['url'],
            'target' => '_self',
            'order' => $child['order'],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "Inserted child: {$child['title']}\n";
    }
}

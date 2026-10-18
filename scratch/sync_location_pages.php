<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\User;
use App\Services\LocationService;
use Illuminate\Contracts\Console\Kernel;

$admin = User::first();
$adminId = $admin ? $admin->id : 1;

$locations = LocationService::all();

foreach ($locations as $key => $loc) {
    $page = Page::firstOrNew(['slug' => $loc['slug']]);

    $isNew = ! $page->exists;
    if ($isNew) {
        $page->user_id = $adminId;
        $page->title = $loc['full_name'];
        $page->template = 'location';
        $page->status = 'published';
        $page->is_indexable = true;
    }

    $page->meta_title = 'Data Recovery Services in '.$loc['name'].' | LifeGuard Lab';
    $page->meta_description = 'Certified Data Recovery in '.$loc['name'].'. Visit our office at '.$loc['address'].' or call '.$loc['phone'].'. Cleanroom recovery for HDD, SSD, RAID & mobile with No Data No Fee guarantee.';
    $page->meta_keywords = 'data recovery '.strtolower($loc['name']).', hard drive recovery '.strtolower($loc['name']).', raid recovery, ssd recovery, lifeguard data recovery';
    $page->template = 'location';
    $page->save();

    echo "Saved Page ID {$page->id} for {$loc['slug']} (".($isNew ? 'CREATED' : 'UPDATED').")\n";
}

// Also ensure 'locations' hub page exists in database
$hub = Page::firstOrNew(['slug' => 'locations']);
if (! $hub->exists) {
    $hub->user_id = $adminId;
    $hub->title = 'Our Office Locations & Cleanroom Laboratories';
    $hub->status = 'published';
    $hub->is_indexable = true;
}
$hub->template = 'locations';
$hub->meta_title = 'LifeGuard Data Recovery Locations | Dubai, Abu Dhabi, Sharjah, India & Kenya';
$hub->meta_description = 'Find your nearest LifeGuard Data Recovery laboratory or drop-off office in Dubai, Abu Dhabi, Sharjah, India, and Kenya. Free evaluation and 24/7 emergency dispatch.';
$hub->save();
echo "Saved Hub Page ID {$hub->id} for locations\n";

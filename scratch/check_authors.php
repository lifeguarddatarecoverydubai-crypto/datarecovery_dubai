<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;

// Replace in post 1091 schema
$p = Post::find(1091);
if ($p && str_contains($p->schema_markup, 'Muskan_lifeguarDubai-1.jpg')) {
    $p->schema_markup = str_replace('Muskan_lifeguarDubai-1.jpg', 'lifeguard-data-recovery-dubai.webp', $p->schema_markup);
    $p->save();
    echo "Post 1091 schema image URL sanitized.\n";
}

echo "Posts with 'muskan' in schema_markup: ".Post::where('schema_markup', 'like', '%muskan%')->count()."\n";
echo "Posts with 'shafia' in schema_markup: ".Post::where('schema_markup', 'like', '%shafia%')->count()."\n";
echo "Pages with 'muskan' in schema_markup: ".Page::where('schema_markup', 'like', '%muskan%')->count()."\n";
echo "Pages with 'shafia' in schema_markup: ".Page::where('schema_markup', 'like', '%shafia%')->count()."\n";
echo "Services with 'muskan' in schema_markup: ".Service::where('schema_markup', 'like', '%muskan%')->count()."\n";
echo "Services with 'shafia' in schema_markup: ".Service::where('schema_markup', 'like', '%shafia%')->count()."\n";

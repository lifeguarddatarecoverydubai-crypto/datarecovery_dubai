<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;

echo "=== PAGES MISSING META DESCRIPTION ===\n";
foreach (Page::whereNull('meta_description')->orWhere('meta_description', '')->get() as $p) {
    echo "- ID: {$p->id} | Slug: {$p->slug} | Title: {$p->title}\n";
}

echo "\n=== PAGES MISSING SCHEMA MARKUP ===\n";
foreach (Page::whereNull('schema_markup')->orWhere('schema_markup', '')->get() as $p) {
    echo "- ID: {$p->id} | Slug: {$p->slug} | Title: {$p->title}\n";
}

echo "\n=== SERVICES AUDIT ===\n";
foreach (Service::all() as $s) {
    echo "- ID: {$s->id} | Slug: {$s->slug} | Title: {$s->title}\n";
    echo "  Focus: '".($s->focus_keyword ?? '')."' | Keywords: '".($s->meta_keywords ?? '')."'\n";
    echo '  Meta Desc ('.strlen($s->meta_description ?? '').' chars): '.substr($s->meta_description ?? '', 0, 80)."...\n";
    echo '  Schema: '.(empty($s->schema_markup) ? 'EMPTY' : 'SET')."\n";
}

echo "\n=== CHECK POSTS FOR INVALID AUTHOR IN SCHEMA MARKUP ===\n";
$postsWithScraperAuthor = Post::where('schema_markup', 'like', '%muskan begum%')
    ->orWhere('schema_markup', 'like', '%shafia begum%')
    ->count();
echo "Posts with scraper personal author in schema_markup: {$postsWithScraperAuthor}\n";

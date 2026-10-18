<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Schema;

echo "=== COLUMNS INSPECTION ===\n";
echo 'Post columns: '.implode(', ', Schema::getColumnListing('posts'))."\n\n";
echo 'Page columns: '.implode(', ', Schema::getColumnListing('pages'))."\n\n";
echo 'Service columns: '.implode(', ', Schema::getColumnListing('services'))."\n\n";

echo '=== POSTS SEO AUDIT (Total: '.Post::count().") ===\n";
$postsMissingFocus = Post::whereNull('focus_keyword')->orWhere('focus_keyword', '')->count();
$postsMissingMetaDesc = Post::whereNull('meta_description')->orWhere('meta_description', '')->count();
$postsMissingKeywords = Post::whereNull('meta_keywords')->orWhere('meta_keywords', '')->count();
$postsMissingSchema = Post::whereNull('schema_markup')->orWhere('schema_markup', '')->count();
echo "- Missing focus_keyword: {$postsMissingFocus}\n";
echo "- Missing meta_description: {$postsMissingMetaDesc}\n";
echo "- Missing meta_keywords: {$postsMissingKeywords}\n";
echo "- Missing schema_markup: {$postsMissingSchema}\n";

echo "\n=== PAGES SEO AUDIT (Total: ".Page::count().") ===\n";
$pagesMissingFocus = Page::whereNull('focus_keyword')->orWhere('focus_keyword', '')->count();
$pagesMissingMetaDesc = Page::whereNull('meta_description')->orWhere('meta_description', '')->count();
$pagesMissingKeywords = Page::whereNull('meta_keywords')->orWhere('meta_keywords', '')->count();
$pagesMissingSchema = Page::whereNull('schema_markup')->orWhere('schema_markup', '')->count();
echo "- Missing focus_keyword: {$pagesMissingFocus}\n";
echo "- Missing meta_description: {$pagesMissingMetaDesc}\n";
echo "- Missing meta_keywords: {$pagesMissingKeywords}\n";
echo "- Missing schema_markup: {$pagesMissingSchema}\n";

echo "\n=== SERVICES SEO AUDIT (Total: ".Service::count().") ===\n";
$servicesMissingFocus = Service::whereNull('focus_keyword')->orWhere('focus_keyword', '')->count();
$servicesMissingMetaDesc = Service::whereNull('meta_description')->orWhere('meta_description', '')->count();
$servicesMissingKeywords = Service::whereNull('meta_keywords')->orWhere('meta_keywords', '')->count();
$servicesMissingSchema = Service::whereNull('schema_markup')->orWhere('schema_markup', '')->count();
echo "- Missing focus_keyword: {$servicesMissingFocus}\n";
echo "- Missing meta_description: {$servicesMissingMetaDesc}\n";
echo "- Missing meta_keywords: {$servicesMissingKeywords}\n";
echo "- Missing schema_markup: {$servicesMissingSchema}\n";

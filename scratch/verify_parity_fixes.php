<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Frontend\SitemapController;
use App\Models\Post;
use App\Models\RedirectRule;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

echo "=== 1. VERIFY AUTHORS & POST COUNTS ===\n";
$dubai = User::where('slug', 'dubailifeguard')->first();
$muskan = User::where('slug', 'muskan-dubai')->first();

echo 'User dubailifeguard: '.($dubai ? "EXISTS (ID: {$dubai->id}, Name: {$dubai->name}, Posts: ".$dubai->posts()->count().')' : 'MISSING')."\n";
echo 'User muskan-dubai: '.($muskan ? "EXISTS (ID: {$muskan->id}, Name: {$muskan->name}, Posts: ".$muskan->posts()->count().')' : 'MISSING')."\n";

echo "\n=== 2. VERIFY TAG COUNTS ===\n";
echo 'Total tags: '.Tag::count()."\n";
echo 'Posts with tags: '.Post::has('tags')->count()." (was 117 previously)\n";
echo 'Total post-tag pivot records: '.DB::table('post_tag')->count()."\n";

echo "\n=== 3. VERIFY AUTHOR SITEMAP XML ===\n";
$sitemapCtrl = app(SitemapController::class);
$authorSitemap = $sitemapCtrl->authors()->getContent();
echo 'Contains /author/dubailifeguard: '.(str_contains($authorSitemap, '/author/dubailifeguard') ? 'YES' : 'NO')."\n";
echo 'Contains /author/muskan-dubai: '.(str_contains($authorSitemap, '/author/muskan-dubai') ? 'YES' : 'NO')."\n";
echo 'Contains /author/ashar-baig (should be NO): '.(str_contains($authorSitemap, '/author/ashar-baig') ? 'YES' : 'NO')."\n";

echo "\n=== 4. VERIFY PAGE SITEMAP XML (EXCLUSIONS) ===\n";
$pageSitemap = $sitemapCtrl->pages()->getContent();
echo 'Contains data-recovery-abu-dhabi-2 (should be NO): '.(str_contains($pageSitemap, 'data-recovery-abu-dhabi-2') ? 'YES' : 'NO')."\n";
echo 'Contains lifeguard-data-recovery-landing-page-ad (should be NO): '.(str_contains($pageSitemap, 'lifeguard-data-recovery-landing-page-ad') ? 'YES' : 'NO')."\n";
echo 'Contains server-data-recovery-ad (should be NO): '.(str_contains($pageSitemap, 'server-data-recovery-ad') ? 'YES' : 'NO')."\n";
echo 'Contains data-recovery-abu-dhabi (canonical location, should be YES): '.(str_contains($pageSitemap, 'data-recovery-abu-dhabi') ? 'YES' : 'NO')."\n";

echo "\n=== 5. VERIFY REDIRECT RULES ===\n";
$rules = [
    '/author/ashar' => '/author/dubailifeguard',
    '/data-recovery-abu-dhabi-2' => '/data-recovery-abu-dhabi',
    '/raid-data-recovery-dubai' => '/raid-data-recovery-service-dubai',
    '/page/raid-data-recovery-service-dubai' => '/raid-data-recovery-service-dubai',
];

foreach ($rules as $from => $to) {
    $rule = RedirectRule::where('source_url', $from)->first();
    echo "Redirect: {$from} => ".($rule ? "{$rule->target_url} (HTTP {$rule->status_code})" : 'MISSING')."\n";
}

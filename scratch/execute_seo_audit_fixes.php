<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\RedirectRule;
use Illuminate\Contracts\Http\Kernel;

echo "=======================================================\n";
echo "  EXECUTING SEO AUDIT FIXES FROM internal_all.csv\n";
echo "=======================================================\n\n";

// --- FIX 1: CANONICAL CONFLICTS IN PAGES ---
echo "--> 1. Fixing circular and redirected canonical URLs in pages...\n";
$canonicalFixes = [
    'raid-data-recovery-service-dubai' => 'https://lifeguarddatarecovery.com/raid-data-recovery-service-dubai/',
    'how-does-mechanical-hard-drive-recovery-work' => 'https://lifeguarddatarecovery.com/how-does-mechanical-hard-drive-recovery-work/',
    'what-is-the-cost-of-data-recovery-in-dubai' => 'https://lifeguarddatarecovery.com/what-is-the-cost-of-data-recovery-in-dubai/',
    'why-data-recovery-is-expensive' => 'https://lifeguarddatarecovery.com/why-data-recovery-is-expensive/',
    'data-recovery-engineer' => 'https://lifeguarddatarecovery.com/data-recovery-engineer/',
    'seo' => 'https://lifeguarddatarecovery.com/seo/',
    'sales-executive' => 'https://lifeguarddatarecovery.com/sales-executive/',
    'social-media-marketing' => 'https://lifeguarddatarecovery.com/social-media-marketing/',
];

foreach ($canonicalFixes as $slug => $canon) {
    $p = Page::where('slug', $slug)->first();
    if ($p) {
        $p->canonical_url = $canon;
        $p->save();
        echo "  [Fixed Canonical] /{$slug} => {$canon}\n";
    }
}

// --- FIX 2: SEED ALL 12 HISTORICAL 301 REDIRECTS ---
echo "\n--> 2. Seeding Screaming Frog 301 redirect rules into redirect_rules table...\n";
$screamingFrogRedirects = [
    ['/hard-drive-data-recovery', '/hard-drive-data-recovery-dubai', 'Redirect legacy hard-drive-data-recovery to canonical'],
    ['/hard-drive-data-recovery/', '/hard-drive-data-recovery-dubai', 'Redirect legacy hard-drive-data-recovery/ to canonical'],
    ['/server-repair-services', '/server-data-recovery-dubai', 'Redirect legacy server-repair-services to canonical'],
    ['/server-repair-services/', '/server-data-recovery-dubai', 'Redirect legacy server-repair-services/ to canonical'],
    ['/raid-data-recovery-service', '/raid-data-recovery-service-dubai', 'Redirect legacy raid-data-recovery-service to canonical'],
    ['/raid-data-recovery-service/', '/raid-data-recovery-service-dubai', 'Redirect legacy raid-data-recovery-service/ to canonical'],
    ['/data-recovery', '/recovering-from-disaster', 'Redirect legacy data-recovery to canonical article'],
    ['/data-recovery/', '/recovering-from-disaster', 'Redirect legacy data-recovery/ to canonical article'],
    ['/iphone-data-recovery-dubai', '/iphone-data-recovery', 'Redirect legacy iphone-data-recovery-dubai to canonical'],
    ['/iphone-data-recovery-dubai/', '/iphone-data-recovery', 'Redirect legacy iphone-data-recovery-dubai/ to canonical'],
    ['/hard-drive-repair', '/hard-drive-data-recovery-dubai', 'Redirect legacy hard-drive-repair to canonical'],
    ['/hard-drive-repair/', '/hard-drive-data-recovery-dubai', 'Redirect legacy hard-drive-repair/ to canonical'],
    ['/data-recovery-in-sharjah', '/data-recovery-sharjah', 'Redirect legacy data-recovery-in-sharjah to canonical'],
    ['/data-recovery-in-sharjah/', '/data-recovery-sharjah', 'Redirect legacy data-recovery-in-sharjah/ to canonical'],
    ['/external-hard-disk-repair', '/external-hard-drive-recovery', 'Redirect legacy external-hard-disk-repair to canonical'],
    ['/external-hard-disk-repair/', '/external-hard-drive-recovery', 'Redirect legacy external-hard-disk-repair/ to canonical'],
    ['/recovery-near-me-best-recovery-center', '/', 'Redirect recovery-near-me-best-recovery-center to home'],
    ['/recovery-near-me-best-recovery-center/', '/', 'Redirect recovery-near-me-best-recovery-center/ to home'],
    ['/%20%20', '/', 'Redirect legacy whitespace permalink to home'],
];

foreach ($screamingFrogRedirects as [$src, $tgt, $desc]) {
    RedirectRule::updateOrCreate(
        ['source_url' => $src],
        [
            'target_url' => $tgt,
            'status_code' => 301,
            'match_type' => 'exact',
            'group' => 'screaming-frog-audit',
            'description' => $desc,
            'is_active' => true,
        ]
    );
    echo "  [301 Redirect] {$src} => {$tgt}\n";
}

// --- FIX 3: DEMOTE SECONDARY H1s IN POST AND PAGE CONTENT ---
echo "\n--> 3. Demoting secondary <h1> tags inside content body to <h2>...\n";
$postsDemoted = 0;
foreach (Post::cursor() as $post) {
    if (preg_match('/<h1(\s|>)/i', $post->content)) {
        $newContent = preg_replace('/<h1(\s|>)/i', '<h2$1', $post->content);
        $newContent = preg_replace('/<\/h1>/i', '</h2>', $newContent);
        if ($newContent !== $post->content) {
            $post->content = $newContent;
            $post->save();
            $postsDemoted++;
        }
    }
}
echo "  Demoted secondary H1s in {$postsDemoted} blog posts.\n";

$pagesDemoted = 0;
foreach (Page::cursor() as $page) {
    if (preg_match('/<h1(\s|>)/i', $page->content)) {
        $newContent = preg_replace('/<h1(\s|>)/i', '<h2$1', $page->content);
        $newContent = preg_replace('/<\/h1>/i', '</h2>', $newContent);
        if ($newContent !== $page->content) {
            $page->content = $newContent;
            $page->save();
            $pagesDemoted++;
        }
    }
}
echo "  Demoted secondary H1s in {$pagesDemoted} pages.\n";

// --- FIX 4: SEED RICH META DESCRIPTIONS FOR ALL 9 CATEGORIES ---
echo "\n--> 4. Populating rich, high-CTR meta descriptions for all 9 categories...\n";
$catDescriptions = [
    'data-recovery' => 'Certified Data Recovery services in Dubai: HDD, SSD, RAID servers, flash drives, and mobile devices recovered in Class 100 cleanroom lab. No Data, No Charge.',
    'case-studies' => 'Real-world data recovery case studies in Dubai: Hard drives, flash NAND, SSDs, RAID servers, and CCTV systems successfully retrieved by LifeGuard experts.',
    'lifeguard-tech' => 'Technical insights into advanced data recovery hardware, PC-3000 diagnostics, cleanroom procedures, and forensic recovery technology in the UAE.',
    'data-backup' => 'Proven enterprise and personal data backup strategies, disaster recovery planning, and automated storage solutions for Dubai and UAE businesses.',
    'data-storage' => 'Authoritative guides on HDD architecture, SSD controller engineering, NVMe technologies, and enterprise storage maintenance.',
    'pc-repair' => 'Expert hardware diagnostics, motherboard repair, and troubleshooting guides for laptops, desktops, and enterprise workstations in Dubai.',
    'blog' => 'Latest industry news, cleanroom recovery updates, cybersecurity bulletins, and storage maintenance tips from LifeGuard Data Recovery Dubai.',
    'blogs' => 'Latest industry news, cleanroom recovery updates, cybersecurity bulletins, and storage maintenance tips from LifeGuard Data Recovery Dubai.',
    'uncategorized' => 'Data recovery articles, troubleshooting guides, and cleanroom lab announcements from LifeGuard Data Recovery Dubai.',
];

foreach ($catDescriptions as $slug => $desc) {
    $c = Category::where('slug', $slug)->first();
    if ($c) {
        $c->description = $desc;
        $c->save();
        echo "  [Category Updated] {$c->name} ({$slug})\n";
    }
}

echo "\n=======================================================\n";
echo "  ALL AUDIT FIXES APPLIED SUCCESSFULLY!\n";
echo "=======================================================\n";

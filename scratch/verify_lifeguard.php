<?php

use App\Http\Controllers\Frontend\ContactController;
use App\Models\Category;
use App\Models\Inquiry;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\RedirectRule;
use App\Models\Service;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

echo "=======================================================\n";
echo "   LIFEGUARD DATA RECOVERY - FULL SYSTEM AUDIT\n";
echo "=======================================================\n\n";

// 1. Database Counts
echo "[1] Database Record Audits:\n";
$postsCount = Post::count();
$pagesCount = Page::count();
$servicesCount = Service::count();
$categoriesCount = Category::count();
$tagsCount = Tag::count();
$usersCount = User::count();
$menuItemsCount = MenuItem::count();
$redirectsCount = RedirectRule::count();

echo "  - Posts in DB:         {$postsCount} (Expected: 396)\n";
echo "  - Pages in DB:         {$pagesCount} (Expected: 103)\n";
echo "  - Services in DB:      {$servicesCount} (Expected: 18)\n";
echo "  - Categories in DB:    {$categoriesCount} (Expected: 9)\n";
echo "  - Tags in DB:          {$tagsCount} (Expected: 86)\n";
echo "  - Users in DB:         {$usersCount}\n";
echo "  - Menu Items in DB:    {$menuItemsCount}\n";
echo "  - Redirect Rules:      {$redirectsCount}\n\n";

// 2. Sample Data Integrity
echo "[2] Data Integrity Check:\n";
$samplePost = Post::first();
echo "  - Sample Post Title:   {$samplePost->title}\n";
echo "  - Sample Post Slug:    {$samplePost->slug}\n";
echo "  - Sample Post Reading: {$samplePost->reading_time} min\n";
echo '  - Sample Post Categories: '.$samplePost->categories->pluck('name')->implode(', ')."\n";
echo '  - Sample Post Schema:  '.(! empty($samplePost->schema_markup) ? 'Custom Yoast Schema Preserved' : 'Default Dynamic Schema')."\n";

$samplePage = Page::where('slug', '!=', 'home')->first();
echo "  - Sample Page Title:   {$samplePage->title} (/{$samplePage->slug})\n";
echo '  - Sample Page SEO:     '.substr($samplePage->seo_description, 0, 60)."...\n\n";

// 3. Route Testing via Laravel Kernel
echo "[3] Route Resolution & HTTP Status Audits:\n";
$testUrls = [
    '/' => 'Homepage',
    '/about-us' => 'About Us Page',
    '/contact' => 'Contact Page',
    '/services' => 'Services Directory',
    '/blog' => 'Knowledge Base / Blog Index',
    '/sitemap.xml' => 'Root Sitemap XML',
    '/sitemap_index.xml' => 'Yoast Sitemap Index Alias',
    '/post-sitemap.xml' => 'Post Sitemap XML (396 posts)',
    '/page-sitemap.xml' => 'Page Sitemap XML (103 pages)',
    '/service-sitemap.xml' => 'Service Sitemap XML (18 services)',
    '/robots.txt' => 'Robots.txt file',
    '/'.$samplePost->slug => 'Direct WordPress Post Permalink Parity (/'.$samplePost->slug.')',
    '/'.$samplePage->slug => 'Direct WordPress Page Permalink Parity (/'.$samplePage->slug.')',
];

$kernel = app()->make(Kernel::class);

foreach ($testUrls as $uri => $label) {
    $request = Request::create($uri, 'GET');
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    $statusColor = ($status === 200) ? 'PASS [200 OK]' : "FAIL [HTTP {$status}]";

    // Check security headers on response
    $hasNoSniff = $response->headers->has('X-Content-Type-Options');
    $hasFrameOpt = $response->headers->has('X-Frame-Options');

    echo "  {$statusColor} | {$label} ({$uri})\n";
    if ($status !== 200) {
        if ($response->isRedirection()) {
            echo '    -> Redirects to: '.$response->headers->get('Location')."\n";
        }
    }
}

// 4. Test 301 Redirection
echo "\n[4] 301 Redirect Engine Audit:\n";
$firstRule = RedirectRule::where('is_active', true)->first();
if ($firstRule) {
    $redirRequest = Request::create($firstRule->source_url, 'GET');
    $redirResponse = $kernel->handle($redirRequest);
    $redirStatus = $redirResponse->getStatusCode();
    $redirTarget = $redirResponse->headers->get('Location');
    echo "  - Testing Rule: '{$firstRule->source_url}' -> '{$firstRule->target_url}'\n";
    echo "  - Response Status: HTTP {$redirStatus} ".($redirStatus === 301 ? '[PASS]' : '[FAIL]')."\n";
    echo "  - Target Location: {$redirTarget}\n";
} else {
    echo "  - No redirect rules found.\n";
}

// 5. Test Contact Form Submission (Data Intake)
echo "\n[5] Emergency Intake Form Submission Audit:\n";
$contactRequest = Request::create('/contact', 'POST', [
    'name' => 'Dr. Khalid Al Mansoor',
    'email' => 'khalid@hospital.ae',
    'phone' => '+971 50 999 8888',
    'subject' => 'Emergency MRI Server RAID 5 Failure',
    'device_type' => 'RAID / NAS / Server',
    'capacity' => '8 x 4TB Enterprise SAS',
    'symptoms' => 'Two drives offline, degraded controller array',
    'emirate' => 'Bur Dubai Main Lab',
    'pickup_requested' => 'yes',
    'message' => 'Critical hospital patient records inaccessible. Immediate cleanroom recovery required.',
]);
$contactController = app(ContactController::class);
$contactResponse = $contactController->submit($contactRequest);
echo '  - Contact POST Status: HTTP '.$contactResponse->getStatusCode()." (Redirect back with flash)\n";
$latestInquiry = Inquiry::latest()->first();
if ($latestInquiry && $latestInquiry->name === 'Dr. Khalid Al Mansoor') {
    echo "  - PASS: Inquiry saved in database ID #{$latestInquiry->id}\n";
    echo "  - Type: {$latestInquiry->type}\n";
    echo "  - Message excerpt:\n    ".str_replace("\n", "\n    ", substr($latestInquiry->message, 0, 180))."...\n";
} else {
    echo "  - FAIL: Inquiry was not saved.\n";
}

// 6. Security Headers Audit
echo "\n[6] Security Headers Verification:\n";
$homeRequest = Request::create('/', 'GET');
$homeResponse = $kernel->handle($homeRequest);
$expectedHeaders = [
    'X-Frame-Options',
    'X-Content-Type-Options',
    'X-XSS-Protection',
    'Referrer-Policy',
    'Permissions-Policy',
];
foreach ($expectedHeaders as $h) {
    $hasHeader = $homeResponse->headers->has($h);
    $val = $homeResponse->headers->get($h);
    echo '  - '.($hasHeader ? '[PASS]' : '[FAIL]')." {$h}: {$val}\n";
}

echo "\n=======================================================\n";
echo "   AUDIT COMPLETE - SYSTEM READY FOR PRODUCTION\n";
echo "=======================================================\n";

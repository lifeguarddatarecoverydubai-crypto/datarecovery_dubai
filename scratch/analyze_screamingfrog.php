<?php

use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$file = 'C:/Users/Graphics/Desktop/internal_all.csv';
$handle = fopen($file, 'r');
$headers = fgetcsv($handle);
$headerMap = array_flip($headers);

$urlCol = $headerMap['Address'] ?? 0;
$statusCol = $headerMap['Status Code'] ?? null;
$indexableCol = $headerMap['Indexability'] ?? null;
$canonicalCol = $headerMap['Canonical Link Element 1'] ?? null;
$redirectUrlCol = $headerMap['Redirect URL'] ?? null;

$missing404s = [];
$canonicalMismatches = [];
$redirects = [];

while (($row = fgetcsv($handle)) !== false) {
    $url = $row[$urlCol] ?? '';
    $status = $row[$statusCol] ?? '';
    $canonical = $row[$canonicalCol] ?? '';
    $redirect = $row[$redirectUrlCol] ?? '';

    if ($status == '404') {
        $missing404s[] = $url;
    }
    if (str_starts_with($status, '3')) {
        $redirects[] = ['from' => $url, 'to' => $redirect];
    }
    if ($status == '200' && $canonical && $canonical !== $url) {
        $canonicalMismatches[] = ['url' => $url, 'canonical' => $canonical];
    }
}
fclose($handle);

echo '=== 404 URLS CHECK IN DATABASE ('.count($missing404s).") ===\n";
foreach ($missing404s as $u) {
    $slug = basename(parse_url($u, PHP_URL_PATH));
    $post = Post::where('slug', $slug)->first();
    $page = Page::where('slug', $slug)->first();
    $status = $post ? ("Found in posts (status: {$post->status})") : ($page ? 'Found in pages' : 'NOT IN DB');
    echo "$slug => $status\n";

    // Also check if any post has this slug as its canonical_url
    $postsWithThisCanonical = Post::where('canonical_url', 'LIKE', "%$slug%")->count();
    if ($postsWithThisCanonical > 0) {
        echo "   -> Pointed to by $postsWithThisCanonical posts as canonical!\n";
    }
}

echo "\n=== CANONICAL MISMATCHES IN DATABASE ===\n";
foreach (['posts', 'pages', 'services', 'products'] as $tbl) {
    $records = DB::table($tbl)->whereNotNull('canonical_url')->where('canonical_url', '!=', '')->get(['id', 'slug', 'canonical_url']);
    $bad = 0;
    $localhost = 0;
    $mismatchSlug = 0;
    foreach ($records as $r) {
        if (str_contains($r->canonical_url, 'localhost') || str_contains($r->canonical_url, '127.0.0.1')) {
            $localhost++;
        }
        $canonSlug = basename(parse_url($r->canonical_url, PHP_URL_PATH));
        if ($canonSlug !== $r->slug) {
            $mismatchSlug++;
        }
    }
    echo "Table: $tbl | Total with canonical: {$records->count()} | Localhost: $localhost | Mismatched Slug: $mismatchSlug\n";
}

echo "\n=== 50-KVA PRODUCT META DESCRIPTION CHECK ===\n";
$prod = Product::where('slug', '50-kva')->first();
if ($prod) {
    echo '50-kva meta_description: '.var_export($prod->meta_description, true)."\n";
    echo '50-kva summary: '.var_export($prod->summary, true)."\n";
    echo '50-kva seo_description accessor: '.var_export($prod->seo_description, true)."\n";
} else {
    echo "50-kva product not found!\n";
}

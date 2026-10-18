<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Str;

function smartFocusKeyword(string $title, string $slug): string
{
    $slugClean = str_replace('-', ' ', $slug);
    $stopWords = [
        'a', 'an', 'the', 'in', 'on', 'at', 'for', 'to', 'of', 'and', 'or', 'is', 'are', 'was', 'were',
        'how', 'what', 'why', 'can', 'does', 'do', 'with', 'from', 'your', 'you', 'our', 'my', 'their',
        'them', 'best', 'guide', 'tips', 'top', 'ways', 'need', 'know', 'after', 'before',
    ];

    $words = explode(' ', $slugClean);
    $meaningful = array_values(array_filter($words, fn ($w) => ! in_array($w, $stopWords) && strlen($w) > 2));

    if (count($meaningful) >= 4) {
        return implode(' ', array_slice($meaningful, 0, 4));
    } elseif (count($meaningful) >= 2) {
        return implode(' ', $meaningful);
    }

    return strtolower(trim(preg_replace('/[^\w\s]/', '', Str::limit($title, 40, ''))));
}

function smartMetaKeywords(string $title, string $focus, ?string $category = null, string $type = 'post'): string
{
    $kw = [];
    $kw[] = $focus;

    // Device / service context
    $lowerTitle = strtolower($title);
    if (str_contains($lowerTitle, 'ssd')) {
        $kw[] = 'ssd data recovery';
        $kw[] = 'ssd data recovery dubai';
    } elseif (str_contains($lowerTitle, 'hard drive') || str_contains($lowerTitle, 'hdd')) {
        $kw[] = 'hard drive data recovery';
        $kw[] = 'hard drive recovery dubai';
    } elseif (str_contains($lowerTitle, 'raid')) {
        $kw[] = 'raid data recovery';
        $kw[] = 'raid server recovery';
    } elseif (str_contains($lowerTitle, 'nas')) {
        $kw[] = 'nas data recovery';
        $kw[] = 'synology data recovery';
    } elseif (str_contains($lowerTitle, 'cctv') || str_contains($lowerTitle, 'dvr')) {
        $kw[] = 'cctv footage recovery';
        $kw[] = 'dvr forensic recovery';
    } elseif (str_contains($lowerTitle, 'iphone') || str_contains($lowerTitle, 'mobile')) {
        $kw[] = 'iphone data recovery';
        $kw[] = 'mobile phone data recovery';
    } elseif (str_contains($lowerTitle, 'sd card') || str_contains($lowerTitle, 'usb')) {
        $kw[] = 'sd card recovery';
        $kw[] = 'usb flash drive recovery';
    } elseif (str_contains($lowerTitle, 'mac') || str_contains($lowerTitle, 'macbook')) {
        $kw[] = 'macbook data recovery';
        $kw[] = 'apple data recovery';
    } elseif (str_contains($lowerTitle, 'ransomware')) {
        $kw[] = 'ransomware data recovery';
        $kw[] = 'ransomware decryption';
    } else {
        $kw[] = 'data recovery services';
        $kw[] = 'data recovery dubai';
    }

    if ($category && ! in_array(strtolower($category), ['blog', 'blogs'])) {
        $kw[] = strtolower($category);
    }

    $kw[] = 'lifeguard data recovery';
    $kw[] = 'cleanroom recovery lab';

    $unique = [];
    foreach ($kw as $item) {
        $clean = trim(strtolower($item));
        if ($clean && ! in_array($clean, $unique, true)) {
            $unique[] = $clean;
        }
    }

    return implode(', ', array_slice($unique, 0, 6));
}

echo "=== TESTING 10 POSTS ===\n";
foreach (Post::take(10)->get() as $p) {
    $cat = $p->categories()->first()?->name;
    $focus = smartFocusKeyword($p->title, $p->slug);
    $keywords = smartMetaKeywords($p->title, $focus, $cat, 'post');
    echo "- Title: {$p->title}\n";
    echo "  Focus: {$focus}\n";
    echo "  Keywords: {$keywords}\n\n";
}

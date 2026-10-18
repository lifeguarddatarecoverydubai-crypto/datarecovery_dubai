<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Str;

function generateFocusKeyword(string $title, string $slug): string
{
    // Strip common fluff words for focus keyword
    $clean = strtolower($title);
    $clean = preg_replace('/[^\w\s-]/', '', $clean);

    // If slug has meaningful keywords
    $slugWords = explode('-', $slug);
    // Remove short stop words from slug
    $stopWords = ['a', 'an', 'the', 'in', 'on', 'at', 'for', 'to', 'of', 'and', 'or', 'is', 'are', 'how', 'what', 'why', 'can', 'does', 'with', 'from', 'your'];
    $filtered = array_filter($slugWords, fn ($w) => ! in_array($w, $stopWords) && strlen($w) > 2);

    if (count($filtered) >= 2) {
        $candidate = implode(' ', array_slice($filtered, 0, 4));

        return $candidate;
    }

    return strtolower(trim(Str::limit($clean, 40, '')));
}

function generateMetaKeywords(string $title, string $focusKeyword, ?string $category = null): string
{
    $keywords = [];
    $keywords[] = $focusKeyword;
    $keywords[] = strtolower($title);

    // Add variations
    if (! str_contains($focusKeyword, 'data recovery')) {
        $keywords[] = $focusKeyword.' data recovery';
    }
    if (! str_contains($focusKeyword, 'dubai')) {
        $keywords[] = $focusKeyword.' dubai';
    }

    if ($category) {
        $keywords[] = strtolower($category);
        $keywords[] = strtolower($category).' recovery';
    }

    $keywords[] = 'lifeguard data recovery';
    $keywords[] = 'cleanroom data recovery';

    // Deduplicate
    $unique = [];
    foreach ($keywords as $k) {
        $trimmed = trim(strtolower($k));
        if ($trimmed && ! in_array($trimmed, $unique)) {
            $unique[] = $trimmed;
        }
    }

    return implode(', ', array_slice($unique, 0, 6));
}

echo "=== SAMPLE POSTS TEST ===\n";
foreach (Post::take(5)->get() as $p) {
    $cat = $p->categories()->first()?->name;
    $focus = generateFocusKeyword($p->title, $p->slug);
    $kw = generateMetaKeywords($p->title, $focus, $cat);
    echo "Title: {$p->title}\n";
    echo "Slug: {$p->slug}\n";
    echo "Focus Keyword: {$focus}\n";
    echo "Meta Keywords: {$kw}\n\n";
}

echo "=== SAMPLE SERVICES TEST ===\n";
foreach (Service::take(3)->get() as $s) {
    $focus = strtolower($s->title);
    $kw = generateMetaKeywords($s->title, $focus, 'Services');
    echo "Service: {$s->title}\n";
    echo "Focus: {$focus}\n";
    echo "Keywords: {$kw}\n\n";
}

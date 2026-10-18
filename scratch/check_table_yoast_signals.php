<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use Illuminate\Contracts\Console\Kernel;

$sampleSlugs = [
    'hard-disk-repair-abu-dhabi',
    'data-services',
    'mac-data-recovery-services',
    'data-recovery-service-for-pc',
    'hard-drive-repair',
    'computer-frozen-after-showing-a-blue-screen',
    'hard-disk-repair-shop-near-me',
];

echo str_pad('TITLE', 40).str_pad('FOCUS KEYWORD', 30).str_pad('YOAST SIGNAL', 25)."AUTHOR\n";
echo str_repeat('-', 105)."\n";

foreach ($sampleSlugs as $slug) {
    $p = Post::where('slug', $slug)->first();
    if ($p) {
        $score = $p->seo_score ?? 0;
        $rating = $score >= 80 ? 'good' : ($score >= 50 ? 'ok' : 'poor');
        $badge = "{$score}/100 (".strtoupper($rating).')';
        echo str_pad(substr($p->title, 0, 38), 40).
             str_pad(substr($p->focus_keyword, 0, 28), 30).
             str_pad($badge, 25).
             ($p->user->name ?? 'None')."\n";
    }
}

<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\Post;
use App\Models\RedirectRule;
use Illuminate\Contracts\Console\Kernel;

$slugs = [
    'why-data-recovery-is-expensive',
    'what-is-the-cost-of-data-recovery-in-dubai',
    'how-does-mechanical-hard-drive-recovery-work',
    'data-recovery-case-studies',
];

foreach ($slugs as $s) {
    $p = Page::where('slug', $s)->orWhere('slug', 'like', "%{$s}%")->get();
    $post = Post::where('slug', $s)->orWhere('slug', 'like', "%{$s}%")->get();
    $r = RedirectRule::where('source_url', 'like', "%{$s}%")->get();

    echo "=== SLUG: {$s} ===\n";
    foreach ($p as $item) {
        echo "  Page ID {$item->id}: slug='{$item->slug}' title='{$item->title}'\n";
    }
    foreach ($post as $item) {
        echo "  Post ID {$item->id}: slug='{$item->slug}' title='{$item->title}'\n";
    }
    foreach ($r as $item) {
        echo "  Redirect: source='{$item->source_url}' => target='{$item->target_url}'\n";
    }
}

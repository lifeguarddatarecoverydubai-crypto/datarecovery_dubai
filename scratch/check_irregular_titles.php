<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use Illuminate\Contracts\Console\Kernel;

$irregular = [];
foreach (Post::all() as $p) {
    // Check if title has mostly lowercase words or starts with lowercase
    $words = explode(' ', $p->title);
    $lowerWords = 0;
    foreach ($words as $w) {
        if (ctype_lower($w) && ! in_array($w, ['a', 'an', 'the', 'and', 'but', 'or', 'for', 'nor', 'on', 'at', 'to', 'from', 'by', 'with', 'in', 'of', 'vs', 'via'])) {
            $lowerWords++;
        }
    }
    if ($lowerWords > 2 || ctype_lower($words[0] ?? '')) {
        $irregular[] = [
            'id' => $p->id,
            'title' => $p->title,
            'meta' => $p->meta_title,
        ];
    }
}

echo 'Found '.count($irregular)." posts with irregular title casing:\n";
foreach (array_slice($irregular, 0, 15) as $item) {
    echo "  [{$item['id']}] {$item['title']}\n";
}

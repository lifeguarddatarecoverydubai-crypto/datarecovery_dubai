<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use Illuminate\Contracts\Http\Kernel;

$remoteInline = [];
$localInline = [];

foreach (Post::cursor() as $post) {
    if (! empty($post->content)) {
        if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $post->content, $matches)) {
            foreach ($matches[1] as $src) {
                if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://')) {
                    if (str_contains($src, 'lifeguarddatarecovery.com')) {
                        $remoteInline[$src] = true;
                    }
                } elseif (str_starts_with($src, '/storage/') || str_starts_with($src, 'storage/')) {
                    $localInline[$src] = true;
                }
            }
        }
    }
}

echo 'Local inline images in blog posts: '.count($localInline)."\n";
echo 'Remote lifeguarddatarecovery.com inline images remaining: '.count($remoteInline)."\n";

if (! empty($remoteInline)) {
    echo "\nSample remaining remote URLs:\n";
    foreach (array_slice(array_keys($remoteInline), 0, 5) as $s) {
        echo "  - {$s}\n";
    }
}

<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Post;
use Illuminate\Contracts\Console\Kernel;

function getAuthorPostsScraped($authorSlug)
{
    $page = 1;
    $allPosts = [];
    while (true) {
        $url = "https://lifeguarddatarecovery.com/author/{$authorSlug}/".($page > 1 ? "page/{$page}/" : '');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        $html = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200 || empty($html)) {
            echo "Finished {$authorSlug} at page {$page} (HTTP {$code})\n";
            break;
        }

        preg_match_all('#href="https://lifeguarddatarecovery\.com/([^"/]+)/?"#i', $html, $matches);
        $slugs = array_values(array_unique($matches[1] ?? []));
        $dbPosts = Post::whereIn('slug', $slugs)->pluck('slug')->toArray();

        // Filter out known static services/pages like 'about-us', 'contact', 'career', 'services', etc.
        $nonPostPages = ['about-us', 'contact', 'career', 'services', 'hard-drive-data-recovery-dubai', 'ssd-drive-data-recovery-in-dubai', 'raid-data-recovery-service-dubai', 'nas-data-recovery-dubai', 'server-data-recovery-dubai', 'cctv-footage-recovery-dubai', 'sd-card-data-recovery', 'iphone-data-recovery', 'ransomware-data-recovery', 'mac-data-recovery'];
        $dbPosts = array_diff($dbPosts, $nonPostPages);

        echo "Page {$page}: found ".count($dbPosts)." post slugs.\n";
        if (empty($dbPosts)) {
            break;
        }

        $beforeCount = count($allPosts);
        $allPosts = array_unique(array_merge($allPosts, $dbPosts));
        if (count($allPosts) === $beforeCount) {
            echo "No new posts added on page {$page}, stopping.\n";
            break;
        }

        $page++;
        if ($page > 45) {
            break;
        }
    }

    return $allPosts;
}

echo "=== SCRAPING DUBAILIFEGUARD ===\n";
$dubaiPosts = getAuthorPostsScraped('dubailifeguard');
echo 'Total dubailifeguard posts: '.count($dubaiPosts)."\n\n";

echo "=== SCRAPING MUSKAN-DUBAI ===\n";
$muskanPosts = getAuthorPostsScraped('muskan-dubai');
echo 'Total muskan-dubai posts: '.count($muskanPosts)."\n\n";

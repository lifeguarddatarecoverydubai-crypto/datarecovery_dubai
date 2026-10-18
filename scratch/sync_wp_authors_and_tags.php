<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\Post;
use App\Models\RedirectRule;
use App\Models\Service;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

echo "=== 1. ENSURE AUTHORS EXIST WITH CORRECT SLUGS ===\n";
// 1. Ashar Baig (dubailifeguard)
$ashar = User::where('slug', 'dubailifeguard')->first();
if (! $ashar) {
    $ashar = User::find(8) ?? User::where('slug', 'ashar')->first();
    if ($ashar) {
        $ashar->name = 'Ashar Baig';
        $ashar->slug = 'dubailifeguard';
        $ashar->save();
        echo "Updated User ID {$ashar->id} to Ashar Baig (slug: dubailifeguard)\n";
    } else {
        $ashar = User::create([
            'name' => 'Ashar Baig',
            'slug' => 'dubailifeguard',
            'email' => 'dubailifeguard@lifeguarddatarecovery.com',
            'password' => bcrypt(Str::random(16)),
        ]);
        echo "Created User ID {$ashar->id} for Ashar Baig (slug: dubailifeguard)\n";
    }
} else {
    echo "User dubailifeguard exists (ID: {$ashar->id})\n";
}

// 2. Muskan Begum (muskan-dubai)
$muskan = User::where('slug', 'muskan-dubai')->first();
if ($muskan) {
    $muskan->name = 'Muskan Begum';
    $muskan->save();
    echo "Verified User ID {$muskan->id} for Muskan Begum (slug: muskan-dubai)\n";
} else {
    $muskan = User::create([
        'name' => 'Muskan Begum',
        'slug' => 'muskan-dubai',
        'email' => 'muskan@lifeguarddatarecovery.com',
        'password' => bcrypt(Str::random(16)),
    ]);
    echo "Created User ID {$muskan->id} for Muskan Begum (slug: muskan-dubai)\n";
}

echo "\n=== 2. FETCH ALL TAGS FROM WP REST API ===\n";
$wpTagsMap = []; // wp_tag_id => local Tag model
for ($p = 1; $p <= 5; $p++) {
    $url = "https://lifeguarddatarecovery.com/wp-json/wp/v2/tags?per_page=100&page={$p}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code !== 200) {
        break;
    }
    $tags = json_decode($res, true);
    if (! is_array($tags) || empty($tags)) {
        break;
    }

    foreach ($tags as $t) {
        $tagName = html_entity_decode($t['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $tagSlug = $t['slug'];
        $localTag = Tag::firstOrCreate(
            ['slug' => $tagSlug],
            ['name' => $tagName]
        );
        $wpTagsMap[$t['id']] = $localTag->id;
    }
}
echo 'Mapped '.count($wpTagsMap)." WP tags to local Tag IDs.\n";

echo "\n=== 3. FETCH POSTS AND SYNC AUTHORS & TAGS ===\n";
$postsProcessed = 0;
$tagsAttached = 0;
$authorCounts = ['dubailifeguard' => 0, 'muskan-dubai' => 0, 'other' => 0];

for ($p = 1; $p <= 5; $p++) {
    $url = "https://lifeguarddatarecovery.com/wp-json/wp/v2/posts?per_page=100&page={$p}&_fields=id,slug,tags,author";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code !== 200) {
        break;
    }
    $posts = json_decode($res, true);
    if (! is_array($posts) || empty($posts)) {
        break;
    }

    foreach ($posts as $wpPost) {
        $slug = $wpPost['slug'];
        $localPost = Post::where('slug', $slug)->first();
        if (! $localPost) {
            continue;
        }

        // 1. Author assignment
        $wpAuthor = $wpPost['author'] ?? 0;
        if ($wpAuthor == 26) {
            $localPost->user_id = $muskan->id;
            $authorCounts['muskan-dubai']++;
        } else {
            // Authors 6, 13, 14 or any other
            $localPost->user_id = $ashar->id;
            $authorCounts['dubailifeguard']++;
        }
        $localPost->save();

        // 2. Tags synchronization
        if (! empty($wpPost['tags']) && is_array($wpPost['tags'])) {
            $localTagIds = [];
            foreach ($wpPost['tags'] as $wpTagId) {
                if (isset($wpTagsMap[$wpTagId])) {
                    $localTagIds[] = $wpTagsMap[$wpTagId];
                }
            }
            if (! empty($localTagIds)) {
                $localPost->tags()->sync($localTagIds);
                $tagsAttached += count($localTagIds);
            }
        }

        $postsProcessed++;
    }
}

echo "Processed {$postsProcessed} posts from WP REST API.\n";
echo "Author assignment: Dubailifeguard: {$authorCounts['dubailifeguard']}, Muskan-dubai: {$authorCounts['muskan-dubai']}\n";
echo "Total tags associations synced: {$tagsAttached}\n";

echo "\n=== 4. CANNIBALIZATION & DUPLICATE CLEANUP ===\n";
// 1. Redirect duplicate /data-recovery-abu-dhabi-2 to /data-recovery-abu-dhabi
RedirectRule::updateOrCreate(
    ['source_url' => '/data-recovery-abu-dhabi-2'],
    ['target_url' => '/data-recovery-abu-dhabi', 'status_code' => 301, 'is_active' => true]
);
RedirectRule::updateOrCreate(
    ['source_url' => '/data-recovery-abu-dhabi-2/'],
    ['target_url' => '/data-recovery-abu-dhabi', 'status_code' => 301, 'is_active' => true]
);
$dupAbuDhabi = Page::where('slug', 'data-recovery-abu-dhabi-2')->first();
if ($dupAbuDhabi) {
    $dupAbuDhabi->delete();
    echo "Deleted duplicate Page row: data-recovery-abu-dhabi-2 (301 redirect in place)\n";
}

// 2. Duplicate RAID page vs Service
RedirectRule::updateOrCreate(
    ['source_url' => '/page/raid-data-recovery-service-dubai'],
    ['target_url' => '/raid-data-recovery-service-dubai', 'status_code' => 301, 'is_active' => true]
);
RedirectRule::updateOrCreate(
    ['source_url' => '/page/raid-data-recovery-service-dubai/'],
    ['target_url' => '/raid-data-recovery-service-dubai', 'status_code' => 301, 'is_active' => true]
);
$dupRaidPage = Page::where('slug', 'raid-data-recovery-service-dubai')->first();
if ($dupRaidPage) {
    $dupRaidPage->delete();
    echo "Deleted duplicate Page row: raid-data-recovery-service-dubai (Canonical Service preserved)\n";
}

// 3. Mark PPC ad landing pages non-indexable
foreach (['lifeguard-data-recovery-landing-page-ad', 'server-data-recovery-ad'] as $adSlug) {
    $adPage = Page::where('slug', $adSlug)->first();
    if ($adPage) {
        $adPage->is_indexable = false;
        $adPage->save();
        echo "Marked {$adSlug} as is_indexable = false (noindex, follow; excluded from sitemap)\n";
    }
}

// 4. Redirect legacy /author/ashar to /author/dubailifeguard
RedirectRule::updateOrCreate(
    ['source_url' => '/author/ashar'],
    ['target_url' => '/author/dubailifeguard', 'status_code' => 301, 'is_active' => true]
);
RedirectRule::updateOrCreate(
    ['source_url' => '/author/ashar/'],
    ['target_url' => '/author/dubailifeguard', 'status_code' => 301, 'is_active' => true]
);
echo "Added 301 redirect for legacy /author/ashar => /author/dubailifeguard\n";

Cache::flush();
echo "Flushed application cache.\n";

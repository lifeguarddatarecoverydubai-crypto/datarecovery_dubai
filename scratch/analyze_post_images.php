<?php

$json = file_get_contents(__DIR__.'/wp_posts_metadata.json');
$posts = json_decode($json, true);

$uniqueImages = [];
$h1Count = 0;
$h2Count = 0;
$h3Count = 0;
$h4Count = 0;

foreach ($posts as $slug => $data) {
    $img = $data['featured_image'];
    if ($img) {
        $uniqueImages[$img] = ($uniqueImages[$img] ?? 0) + 1;
    }
    if (stripos($data['content'], '<h1') !== false) {
        $h1Count++;
    }
    if (stripos($data['content'], '<h2') !== false) {
        $h2Count++;
    }
    if (stripos($data['content'], '<h3') !== false) {
        $h3Count++;
    }
    if (stripos($data['content'], '<h4') !== false) {
        $h4Count++;
    }
}

echo 'Total Posts: '.count($posts)."\n";
echo 'Total Unique Featured Images in WP: '.count($uniqueImages)."\n";
echo "Posts with <h1> in content: $h1Count\n";
echo "Posts with <h2> in content: $h2Count\n";
echo "Posts with <h3> in content: $h3Count\n";
echo "Posts with <h4> in content: $h4Count\n";

echo "\nTop 10 Most Common Images:\n";
arsort($uniqueImages);
foreach (array_slice($uniqueImages, 0, 10) as $url => $cnt) {
    echo "  $cnt times: $url\n";
}

echo "\nSample of 10 distinct images:\n";
foreach (array_slice(array_keys($uniqueImages), 0, 10) as $url) {
    echo "  $url\n";
}

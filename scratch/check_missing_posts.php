<?php

$postsJson = file_get_contents(__DIR__.'/wp_posts_metadata.json');
$posts = json_decode($postsJson, true);

$imagesJson = file_get_contents(__DIR__.'/downloaded_wp_images.json');
$downloaded = json_decode($imagesJson, true);

$missingPosts = [];
foreach ($posts as $slug => $data) {
    $imgUrl = $data['featured_image'];
    if (! isset($downloaded[$imgUrl])) {
        $missingPosts[$slug] = [
            'title' => $data['title'],
            'wp_img' => $imgUrl,
        ];
    }
}

echo 'Posts without downloaded image: '.count($missingPosts)."\n";
print_r($missingPosts);

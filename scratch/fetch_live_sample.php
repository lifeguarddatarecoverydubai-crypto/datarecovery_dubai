<?php

$url = 'https://lifeguarddatarecovery.com/wp-json/wp/v2/posts?slug=what-causes-ssd-slowdowns-and-how-to-fix-them&_embed=1';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo 'Response Length: '.strlen($response)."\n";

$data = json_decode($response, true);
if (! empty($data[0])) {
    $p = $data[0];
    echo 'WP ID: '.$p['id']."\n";
    echo 'Title: '.$p['title']['rendered']."\n";
    echo 'Content Length: '.strlen($p['content']['rendered'])."\n";
    echo 'Featured Media ID: '.$p['featured_media']."\n";
    if (! empty($p['_embedded']['wp:featuredmedia'][0])) {
        echo 'Featured Media URL: '.$p['_embedded']['wp:featuredmedia'][0]['source_url']."\n";
    }

    // Check images in content
    preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $p['content']['rendered'], $images);
    echo 'Images inside WP content ('.count($images[1])."):\n";
    foreach ($images[1] as $img) {
        echo '  '.$img."\n";
    }

    // Check headings
    preg_match_all('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/i', $p['content']['rendered'], $headings);
    echo 'Headings in WP content ('.count($headings[0])."):\n";
    foreach ($headings[0] as $h) {
        echo '  '.strip_tags($h)."\n";
    }
}

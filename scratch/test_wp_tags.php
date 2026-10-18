<?php

$allTags = [];
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
        $allTags[$t['id']] = [
            'name' => html_entity_decode($t['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'slug' => $t['slug'],
        ];
    }
}
echo 'Total tags found: '.count($allTags)."\n";
print_r(array_slice($allTags, 0, 5, true));

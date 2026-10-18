<?php

$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=lifeguard_dubai', 'root', '');

// Check contact page content
$stmt = $pdo->prepare("SELECT title, content FROM pages WHERE slug = 'contact' OR slug = 'about-us' LIMIT 2");
$stmt->execute();
$pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($pages as $p) {
    echo "=== Page: {$p['title']} ===\n";
    // strip HTML and find phones, emails, addresses
    $text = strip_tags($p['content']);
    preg_match_all('/(?:\+971[\d\s\-]{7,15}|04[\d\s\-]{6,10}|05[\d\s\-]{7,10})/i', $text, $phones);
    echo 'Phones: '.implode(', ', array_unique($phones[0] ?? []))."\n";

    preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/i', $text, $emails);
    echo 'Emails: '.implode(', ', array_unique($emails[0] ?? []))."\n";

    // sample lines
    $lines = array_filter(array_map('trim', explode("\n", $text)));
    foreach (array_slice($lines, 0, 15) as $line) {
        if (strlen($line) > 10) {
            echo '  '.substr($line, 0, 100)."\n";
        }
    }
}

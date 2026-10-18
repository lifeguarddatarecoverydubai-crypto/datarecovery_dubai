<?php

$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=lifeguard_dubai', 'root', '');

$tables = ['posts', 'pages', 'services', 'categories', 'tags', 'menu_items', 'redirect_rules'];

foreach ($tables as $table) {
    echo "=== Table: {$table} ===\n";
    $cols = $pdo->query("SHOW COLUMNS FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        echo "  - {$col['Field']} ({$col['Type']})\n";
    }
}

<?php

$pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');

$dbs = ['lifeguard_dubai', 'saudia_site', 'recoverydata_db'];

foreach ($dbs as $db) {
    echo "=== DB: {$db} ===\n";
    try {
        $tables = $pdo->query("SHOW TABLES FROM `{$db}`")->fetchAll(PDO::FETCH_COLUMN);
        foreach (['posts', 'pages', 'services', 'products', 'categories', 'tags', 'redirect_rules', 'menu_items'] as $tbl) {
            if (in_array($tbl, $tables)) {
                $count = $pdo->query("SELECT COUNT(*) FROM `{$db}`.`{$tbl}`")->fetchColumn();
                echo "  {$tbl}: {$count}\n";
            } else {
                echo "  {$tbl}: DOES NOT EXIST\n";
            }
        }

        // Sample 3 posts
        if (in_array('posts', $tables)) {
            $stmt = $pdo->query("SELECT id, title, slug FROM `{$db}`.`posts` LIMIT 3");
            echo "  Sample posts:\n";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "    [{$row['id']}] {$row['slug']} - {$row['title']}\n";
            }
        }
    } catch (Exception $e) {
        echo '  Error: '.$e->getMessage()."\n";
    }
}

<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SEARCHING FOR /services/ LINKS IN DB ===\n";
foreach (['posts', 'pages', 'products', 'services', 'menus', 'menu_items'] as $tbl) {
    if (! Schema::hasTable($tbl)) {
        continue;
    }
    $cols = Schema::getColumnListing($tbl);
    foreach ($cols as $c) {
        try {
            $count = DB::table($tbl)->where($c, 'LIKE', '%/services/%')->count();
            if ($count > 0) {
                echo "Found $count in $tbl.$c\n";
                // Let's sample a few
                $samples = DB::table($tbl)->where($c, 'LIKE', '%/services/%')->take(3)->pluck($c);
                foreach ($samples as $s) {
                    preg_match_all('#https?://[^"\s\']+?/services/[a-zA-Z0-9\-_]+|/services/[a-zA-Z0-9\-_]+#', $s, $m);
                    if (! empty($m[0])) {
                        echo '   Sample URLs: '.implode(', ', array_unique($m[0]))."\n";
                    }
                }
            }
        } catch (Throwable $e) {
        }
    }
}

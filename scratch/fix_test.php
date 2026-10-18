<?php

$f = 'tests/Feature/RecoveryDataSiteTest.php';
$c = file_get_contents($f);
$c = str_replace(
    "\$response->assertSee('Related Case Studies & Technical Guides');",
    "\$response->assertSee('Related Case Studies & Technical Guides', false);",
    $c
);
file_put_contents($f, $c);
echo "Updated escape flag in test\n";

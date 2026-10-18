<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\OdooPage;
use Illuminate\Contracts\Console\Kernel;

$odoo = OdooPage::first();
if ($odoo) {
    echo "Found OdooPage in DB. Updating contact email and phone...\n";
    $odoo->update([
        'contact_phone' => '+971 43 933 082',
        'contact_email' => 'info@lifeguarddatarecovery.com',
    ]);
    echo "Updated! Email: {$odoo->contact_email}, Phone: {$odoo->contact_phone}\n";
} else {
    echo "No OdooPage in DB.\n";
}

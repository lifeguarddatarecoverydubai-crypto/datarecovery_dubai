<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

$admin = User::where('role', 'admin')->first() ?: User::first();
echo "Admin email: {$admin->email}, two_factor_confirmed_at: {$admin->two_factor_confirmed_at}\n";

$req = Request::create('/admin', 'GET');
$res = $app->handle($req);
echo 'Status: '.$res->getStatusCode()."\n";
echo 'Redirect Target: '.$res->headers->get('Location')."\n";

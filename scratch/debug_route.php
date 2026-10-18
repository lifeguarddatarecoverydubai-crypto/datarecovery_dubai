<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$req = Request::create('/hard-drive-data-recovery-dubai', 'GET');
$route = Route::getRoutes()->match($req);
echo 'Matched route URI: '.$route->uri()."\n";
echo 'Action: '.json_encode($route->getActionName())."\n";

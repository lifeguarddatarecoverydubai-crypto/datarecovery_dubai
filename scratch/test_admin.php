<?php

use App\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'admin@example.com')->first();
if (! $user) {
    echo "Admin user not found!\n";
    exit(1);
}

$user->two_factor_secret = 'JBSWY3DPEHPK3PXP';
$user->two_factor_confirmed_at = now();

$request = Request::create('/admin', 'GET');
$app->instance('request', $request);

// Log in as admin
auth()->login($user);

$response = $kernel->handle($request);

echo "Admin User: {$user->name} ({$user->email})\n";
echo 'Admin Dashboard HTTP Status: '.$response->getStatusCode()."\n";
echo 'Redirect Target: '.$response->headers->get('Location')."\n";

$pagesRequest = Request::create('/admin/pages', 'GET');
$pagesResponse = $kernel->handle($pagesRequest);
echo 'Admin Pages List HTTP Status: '.$pagesResponse->getStatusCode()."\n";

$postsRequest = Request::create('/admin/posts', 'GET');
$postsResponse = $kernel->handle($postsRequest);
echo 'Admin Posts List HTTP Status: '.$postsResponse->getStatusCode()."\n";

$servicesRequest = Request::create('/admin/services', 'GET');
$servicesResponse = $kernel->handle($servicesRequest);
echo 'Admin Services List HTTP Status: '.$servicesResponse->getStatusCode()."\n";

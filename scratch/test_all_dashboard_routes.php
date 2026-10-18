<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== SYSTEMATIC ADMIN DASHBOARD AUDIT ===\n";

$admin = User::where('role', 'admin')->first() ?: User::first();
if (! $admin) {
    echo "No user found in DB!\n";
    exit(1);
}
echo "Authenticating as: {$admin->email} (ID: {$admin->id}, Role: {$admin->role})\n";

// Ensure 2FA is marked confirmed in session/user for test
Auth::login($admin);
session(['two_factor_confirmed' => true]);

$samplePost = Post::first();
$samplePage = Page::first();
$sampleService = Service::first();

$routes = [
    'Dashboard' => '/admin',
    'Posts Index' => '/admin/posts',
    'Posts Create' => '/admin/posts/create',
    'Posts Edit' => $samplePost ? "/admin/posts/{$samplePost->id}/edit" : null,
    'Pages Index' => '/admin/pages',
    'Pages Create' => '/admin/pages/create',
    'Pages Edit' => $samplePage ? "/admin/pages/{$samplePage->id}/edit" : null,
    'Services Index' => '/admin/services',
    'Services Create' => '/admin/services/create',
    'Services Edit' => $sampleService ? "/admin/services/{$sampleService->id}/edit" : null,
    'Categories Index' => '/admin/categories',
    'Tags Index' => '/admin/tags',
    'Media Library' => '/admin/media',
    'Media Modal AJAX' => '/admin/media-modal-list',
    'Menus Index' => '/admin/menus',
    'Redirects Manager' => '/admin/redirects',
    'Inquiries Index' => '/admin/inquiries',
    'Users Index' => '/admin/users',
    'Profile Settings' => '/admin/profile',
    'Odoo Page Edit' => '/admin/odoo',
];

$passedCount = 0;
$failedCount = 0;

foreach ($routes as $name => $uri) {
    if (! $uri) {
        echo "[-] $name: Skipped (no model)\n";

        continue;
    }

    $req = Request::create($uri, 'GET');
    $req->setUserResolver(fn () => $admin);
    $req->setLaravelSession(app('session.store'));

    try {
        $res = $app->handle($req);
        $code = $res->getStatusCode();
        if ($code === 200) {
            echo "[PASS] $name ($uri) -> 200 OK\n";
            $passedCount++;
        } else {
            echo "[FAIL] $name ($uri) -> Status $code\n";
            $failedCount++;
        }
    } catch (Throwable $e) {
        echo "[ERROR] $name ($uri) -> ".$e->getMessage()."\n";
        $failedCount++;
    }
}

echo "\nDashboard Routes Tested: ".($passedCount + $failedCount)."\n";
echo "Passed: $passedCount\n";
echo "Failed: $failedCount\n";

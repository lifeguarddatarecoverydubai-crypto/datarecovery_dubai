<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Services\WebpConverterService;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

$converter = app(WebpConverterService::class);

echo "1. Downloading Favicon from https://lifeguarddatarecovery.com/wp-content/uploads/2022/03/fav.png ...\n";
$favRes = Http::withoutVerifying()->timeout(15)->get('https://lifeguarddatarecovery.com/wp-content/uploads/2022/03/fav.png');
if ($favRes->successful()) {
    $favBytes = $favRes->body();
    File::put(public_path('favicon.ico'), $favBytes);
    File::put(public_path('favicon.png'), $favBytes);
    File::put(public_path('apple-touch-icon.png'), $favBytes);
    echo "  Saved favicon.ico, favicon.png, apple-touch-icon.png to public/\n";

    $favWebp = $converter->convertBinary($favBytes, 'favicon.png', 'media', 90);
    if ($favWebp) {
        echo '  Saved favicon WebP to '.$favWebp['url'].' ('.$favWebp['size']." bytes)\n";
    }
} else {
    echo "  Failed to download fav.png!\n";
}

echo "\n2. Downloading Logo from https://lifeguarddatarecovery.com/wp-content/uploads/2024/08/logo.png ...\n";
$logoRes = Http::withoutVerifying()->timeout(15)->get('https://lifeguarddatarecovery.com/wp-content/uploads/2024/08/logo.png');
if (! $logoRes->successful()) {
    echo "  Trying fallback 2022/03/logo.png ...\n";
    $logoRes = Http::withoutVerifying()->timeout(15)->get('https://lifeguarddatarecovery.com/wp-content/uploads/2022/03/logo.png');
}

if ($logoRes->successful()) {
    $logoBytes = $logoRes->body();
    $imgDir = public_path('images');
    if (! is_dir($imgDir)) {
        mkdir($imgDir, 0755, true);
    }
    File::put($imgDir.'/logo.png', $logoBytes);
    echo '  Saved logo.png to public/images/logo.png ('.strlen($logoBytes)." bytes)\n";

    $logoWebp = $converter->convertBinary($logoBytes, 'logo.png', 'images', 85);
    if ($logoWebp) {
        // Also ensure public/images/logo.webp exists
        @copy(storage_path('app/public/images/logo.webp'), public_path('images/logo.webp'));
        echo '  Converted logo to WebP: '.$logoWebp['url'].' ('.$logoWebp['size'].' bytes, '.$logoWebp['dimensions'].")\n";
    }
} else {
    echo "  Failed to download logo.png!\n";
}

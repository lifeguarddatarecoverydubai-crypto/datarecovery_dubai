<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebpConverterService
{
    /**
     * Supported image mime types for WebP conversion.
     */
    protected const CONVERTIBLE_MIMES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
        'image/gif',
        'image/bmp',
        'image/x-ms-bmp',
        'image/avif',
    ];

    /**
     * Supported image extensions for WebP conversion.
     */
    protected const CONVERTIBLE_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'avif',
    ];

    /**
     * Default WebP compression quality (1-100).
     * 82 provides excellent visual fidelity while cutting file size by 50-80%.
     */
    protected int $defaultQuality = 82;

    /**
     * Maximum allowed width in pixels before downscaling.
     */
    protected int $maxWidth = 2048;

    /**
     * Check if a mime type or extension is convertible to WebP.
     */
    public function isConvertible(?string $mime = null, ?string $extension = null): bool
    {
        if ($mime) {
            $mime = strtolower(trim(explode(';', $mime)[0]));
            if (in_array($mime, self::CONVERTIBLE_MIMES, true)) {
                return true;
            }
        }

        if ($extension) {
            $ext = strtolower(ltrim(trim($extension), '.'));
            if (in_array($ext, self::CONVERTIBLE_EXTENSIONS, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert an incoming UploadedFile to optimized WebP.
     *
     * @param  string  $subfolder  e.g. 'uploads/2026/10'
     */
    public function convertUploadedFile(
        UploadedFile $file,
        string $subfolder = '',
        ?int $quality = null,
        ?int $maxWidth = null
    ): array {
        $quality = $quality ?? $this->defaultQuality;
        $maxWidth = $maxWidth ?? $this->maxWidth;

        if (empty($subfolder)) {
            $subfolder = 'uploads/'.date('Y/m');
        }
        $subfolder = trim($subfolder, '/');

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBase = Str::slug($originalName);
        if (empty($safeBase)) {
            $safeBase = 'image';
        }
        $fileName = $safeBase.'-'.time().'-'.Str::lower(Str::random(6)).'.webp';
        $relativePath = $subfolder.'/'.$fileName;

        $binary = file_get_contents($file->getRealPath());
        $sourcePath = $file->getRealPath();

        return $this->processAndSave(
            binary: $binary,
            fileName: $fileName,
            relativePath: $relativePath,
            originalName: $file->getClientOriginalName(),
            quality: $quality,
            maxWidth: $maxWidth,
            sourcePathForExif: $sourcePath
        );
    }

    /**
     * Convert raw binary image data into optimized WebP and save to storage.
     *
     * @param  string  $preferredName  e.g. 'hero-banner.jpg'
     * @param  string  $subfolder  e.g. 'media/2026/10'
     */
    public function convertBinary(
        string $binary,
        string $preferredName,
        string $subfolder = 'media',
        ?int $quality = null,
        ?int $maxWidth = null
    ): ?array {
        if (empty($binary)) {
            return null;
        }

        $quality = $quality ?? $this->defaultQuality;
        $maxWidth = $maxWidth ?? $this->maxWidth;
        $subfolder = trim($subfolder, '/');

        $originalBase = pathinfo($preferredName, PATHINFO_FILENAME);
        $safeBase = Str::slug($originalBase);
        if (empty($safeBase)) {
            $safeBase = 'img-'.Str::random(8);
        }

        $fileName = $safeBase.'.webp';
        $relativePath = $subfolder.'/'.$fileName;

        return $this->processAndSave(
            binary: $binary,
            fileName: $fileName,
            relativePath: $relativePath,
            originalName: $preferredName,
            quality: $quality,
            maxWidth: $maxWidth
        );
    }

    /**
     * Download a remote image URL, convert to optimized WebP, and save to local storage.
     */
    public function convertUrl(
        string $url,
        ?string $customSubfolder = null,
        ?int $quality = null,
        ?int $maxWidth = null
    ): ?array {
        $cleanUrl = trim($url);
        if (empty($cleanUrl) || ! filter_var($cleanUrl, FILTER_VALIDATE_URL)) {
            return null;
        }

        try {
            $response = Http::timeout(25)
                ->withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
                    'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                ])
                ->get($cleanUrl);

            if (! $response->successful()) {
                return null;
            }

            $binary = $response->body();
            if (empty($binary) || strlen($binary) < 64) {
                return null;
            }

            // Determine filename and path preserving WordPress year/month folder structure if present
            $parsedUrl = parse_url($cleanUrl, PHP_URL_PATH);
            $basename = basename($parsedUrl);
            $filenameWithoutExt = pathinfo($basename, PATHINFO_FILENAME);

            // Check if URL has wp-content/uploads/YYYY/MM pattern
            $subfolder = $customSubfolder;
            if ($subfolder === null) {
                if (preg_match('#wp-content/uploads/(\d{4}/\d{2})#i', $parsedUrl, $matches)) {
                    $subfolder = 'media/'.$matches[1];
                } elseif (preg_match('#wp-content/uploads/elementor/thumbs#i', $parsedUrl)) {
                    $subfolder = 'media/thumbs';
                } elseif (preg_match('#wp-content/uploads/(\d{4})#i', $parsedUrl, $matches)) {
                    $subfolder = 'media/'.$matches[1];
                } else {
                    $subfolder = 'media/'.date('Y/m');
                }
            }

            $safeBase = Str::slug($filenameWithoutExt);
            if (empty($safeBase)) {
                $safeBase = 'media-'.Str::random(8);
            }

            $fileName = $safeBase.'.webp';
            $relativePath = trim($subfolder, '/').'/'.$fileName;

            return $this->processAndSave(
                binary: $binary,
                fileName: $fileName,
                relativePath: $relativePath,
                originalName: $basename,
                quality: $quality ?? $this->defaultQuality,
                maxWidth: $maxWidth ?? $this->maxWidth
            );
        } catch (\Throwable $e) {
            Log::warning("WebpConverterService failed for URL [{$cleanUrl}]: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Convert an existing local image on disk to WebP.
     */
    public function convertLocalFile(
        string $absoluteSourcePath,
        string $targetRelativePath,
        ?int $quality = null,
        ?int $maxWidth = null
    ): ?array {
        if (! file_exists($absoluteSourcePath)) {
            return null;
        }

        $binary = file_get_contents($absoluteSourcePath);
        $fileName = basename($targetRelativePath);

        return $this->processAndSave(
            binary: $binary,
            fileName: $fileName,
            relativePath: $targetRelativePath,
            originalName: basename($absoluteSourcePath),
            quality: $quality ?? $this->defaultQuality,
            maxWidth: $maxWidth ?? $this->maxWidth,
            sourcePathForExif: $absoluteSourcePath
        );
    }

    /**
     * Core processing routine: loads binary, auto-orients, preserves alpha,
     * bounds dimensions, compresses to WebP, writes to both app and public storage.
     */
    protected function processAndSave(
        string $binary,
        string $fileName,
        string $relativePath,
        string $originalName,
        int $quality,
        int $maxWidth,
        ?string $sourcePathForExif = null
    ): array {
        $originalSize = strlen($binary);

        // Attempt to create GD image resource from string
        $image = @imagecreatefromstring($binary);

        if (! $image) {
            throw new \RuntimeException("Unable to decode image data for [{$fileName}]. Unsupported or corrupt image format.");
        }

        // Auto-orient based on EXIF if available
        if ($sourcePathForExif && function_exists('exif_read_data')) {
            $image = $this->autoOrientExif($image, $sourcePathForExif);
        }

        $origWidth = imagesx($image);
        $origHeight = imagesy($image);

        // Ensure palette images (like indexed PNG/GIF) are converted to truecolor
        imagepalettetotruecolor($image);

        // Preserve alpha transparency
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // Downscale if width exceeds maxWidth maintaining aspect ratio
        if ($origWidth > $maxWidth && $maxWidth > 0) {
            $newWidth = $maxWidth;
            $newHeight = (int) round(($origHeight / $origWidth) * $maxWidth);

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);

            $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
            imagedestroy($image);
            $image = $resized;

            $finalWidth = $newWidth;
            $finalHeight = $newHeight;
        } else {
            $finalWidth = $origWidth;
            $finalHeight = $origHeight;
        }

        // Prepare destination paths
        $appStorageFile = storage_path('app/public/'.$relativePath);
        $publicFile = public_path('storage/'.$relativePath);

        $dirApp = dirname($appStorageFile);
        if (! is_dir($dirApp)) {
            mkdir($dirApp, 0755, true);
        }

        $dirPublic = dirname($publicFile);
        if (! is_dir($dirPublic)) {
            mkdir($dirPublic, 0755, true);
        }

        // Encode and save WebP
        $success = imagewebp($image, $appStorageFile, $quality);
        imagedestroy($image);

        if (! $success || ! file_exists($appStorageFile)) {
            throw new \RuntimeException("Failed to write WebP image to [{$appStorageFile}].");
        }

        // Mirror to public/storage for direct web server access
        @copy($appStorageFile, $publicFile);

        $newSize = filesize($appStorageFile);

        return [
            'original_name' => $originalName,
            'file_name' => $fileName,
            'file_path' => $relativePath,
            'disk' => 'public',
            'mime_type' => 'image/webp',
            'size' => $newSize,
            'dimensions' => "{$finalWidth}x{$finalHeight}",
            'saved_bytes' => max(0, $originalSize - $newSize),
            'url' => '/storage/'.$relativePath,
        ];
    }

    /**
     * Rotate GD image based on EXIF orientation flag.
     */
    protected function autoOrientExif($image, string $path)
    {
        try {
            $exif = @exif_read_data($path);
            if (! empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $rotated = imagerotate($image, 180, 0);
                        imagedestroy($image);

                        return $rotated;
                    case 6:
                        $rotated = imagerotate($image, -90, 0);
                        imagedestroy($image);

                        return $rotated;
                    case 8:
                        $rotated = imagerotate($image, 90, 0);
                        imagedestroy($image);

                        return $rotated;
                }
            }
        } catch (\Throwable) {
            // Ignore EXIF read errors
        }

        return $image;
    }
}

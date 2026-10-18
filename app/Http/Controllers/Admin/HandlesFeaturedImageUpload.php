<?php

namespace App\Http\Controllers\Admin;

use App\Models\Media;
use App\Services\WebpConverterService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

trait HandlesFeaturedImageUpload
{
    /**
     * Handle featured image upload or conversion to WebP with reduced file size.
     */
    protected function handleFeaturedImage(Request $request, ?string $currentImage = null, string $subfolder = 'uploads'): ?string
    {
        $webpConverter = app(WebpConverterService::class);

        // 1. Direct file upload via featured_image_file or featured_image
        $uploadedFile = null;
        if ($request->hasFile('featured_image_file')) {
            $uploadedFile = $request->file('featured_image_file');
        } elseif ($request->hasFile('featured_image')) {
            $uploadedFile = $request->file('featured_image');
        }

        if ($uploadedFile instanceof UploadedFile && $uploadedFile->isValid()) {
            if ($webpConverter->isConvertible($uploadedFile->getMimeType(), $uploadedFile->getClientOriginalExtension())) {
                try {
                    $converted = $webpConverter->convertUploadedFile($uploadedFile, $subfolder.'/'.date('Y/m'), 82, 1600);

                    // Create Media record so it's accessible in Media Library
                    Media::create([
                        'user_id' => auth()->id(),
                        'name' => pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME),
                        'file_name' => $converted['file_name'],
                        'file_path' => $converted['file_path'],
                        'disk' => $converted['disk'],
                        'mime_type' => $converted['mime_type'],
                        'size' => $converted['size'],
                        'dimensions' => $converted['dimensions'],
                    ]);

                    return '/storage/'.$converted['file_path'];
                } catch (\Throwable $e) {
                    Log::warning('WebP conversion failed on featured image upload: '.$e->getMessage());
                }
            }
        }

        // 2. String input provided in featured_image
        $imageString = $request->input('featured_image');
        if (! empty($imageString) && is_string($imageString)) {
            $imageString = trim($imageString);

            // If it's a remote URL from an external source, download and convert to WebP
            if (str_starts_with($imageString, 'http://') || str_starts_with($imageString, 'https://')) {
                // If it's pointing to localhost/current app, keep local path
                $parsed = parse_url($imageString);
                $appHost = parse_url(config('app.url'), PHP_URL_HOST);
                if (empty($parsed['host']) || $parsed['host'] === $appHost || in_array($parsed['host'], ['localhost', '127.0.0.1'], true)) {
                    return $parsed['path'] ?? $imageString;
                }

                try {
                    $result = $webpConverter->convertUrl($imageString, $subfolder.'/'.date('Y/m'), 82, 1600);
                    if ($result && ! empty($result['file_path'])) {
                        return '/storage/'.$result['file_path'];
                    }
                } catch (\Throwable $e) {
                    Log::warning('Remote image conversion to WebP failed: '.$e->getMessage());
                }
            }

            return $imageString;
        }

        return $currentImage;
    }
}

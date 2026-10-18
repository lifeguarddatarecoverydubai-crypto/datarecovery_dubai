<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\WebpConverterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaController extends Controller
{
    /**
     * Display the media library.
     */
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->query('search');
        $type = $request->query('type');

        $query = Media::with('user')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%")
                    ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        if ($type === 'image') {
            $query->where('mime_type', 'like', 'image/%');
        } elseif ($type === 'document') {
            $query->where('mime_type', 'like', 'application/%')
                ->orWhere('mime_type', 'like', 'text/%');
        }

        $media = $query->paginate(24)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($media);
        }

        return view('admin.media.index', compact('media', 'search', 'type'));
    }

    /**
     * Upload one or multiple media files.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $allowedMimes = 'jpeg,jpg,png,webp,gif,svg,pdf,doc,docx,xlsx,csv,zip,txt';
        $request->validate([
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:20480', 'mimes:'.$allowedMimes], // max 20MB
            'file' => ['nullable', 'file', 'max:20480', 'mimes:'.$allowedMimes],
        ]);

        $uploadedFiles = [];
        $filesToProcess = [];

        if ($request->hasFile('files')) {
            $filesToProcess = $request->file('files');
        } elseif ($request->hasFile('file')) {
            $filesToProcess = [$request->file('file')];
        }

        if (empty($filesToProcess)) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'No files provided.'], 422);
            }

            return back()->with('error', 'Please select a file to upload.');
        }

        $disallowedExtensions = [
            'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'phps', 'phar',
            'sh', 'bash', 'cgi', 'pl', 'py', 'exe', 'bat', 'cmd', 'vbs', 'jsp',
            'asp', 'aspx', 'shtml', 'env', 'htaccess', 'htpasswd', 'config', 'html', 'htm',
        ];
        $allowedExtensions = ['jpeg', 'jpg', 'png', 'webp', 'gif', 'svg', 'pdf', 'doc', 'docx', 'xlsx', 'csv', 'zip', 'txt'];

        foreach ($filesToProcess as $file) {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = strtolower($file->getClientOriginalExtension());

            if (in_array($extension, $disallowedExtensions, true) || ! in_array($extension, $allowedExtensions, true)) {
                continue;
            }

            if ($extension === 'svg') {
                $svgContent = file_get_contents($file->getRealPath());
                if (preg_match('/<script|javascript:|onload=|onerror=|onclick=/i', $svgContent)) {
                    if ($request->wantsJson()) {
                        return response()->json(['error' => 'Security Error: Malicious script detected in SVG file.'], 422);
                    }

                    return back()->with('error', 'Security Error: Malicious SVG payload detected.');
                }
            }

            $safeName = Str::slug($originalName).'-'.time().'-'.Str::random(6).'.'.$extension;

            $webpConverter = app(WebpConverterService::class);
            if ($webpConverter->isConvertible($file->getMimeType(), $extension)) {
                try {
                    $converted = $webpConverter->convertUploadedFile($file, 'uploads/'.date('Y/m'));
                    $path = $converted['file_path'];
                    $safeName = $converted['file_name'];
                    $mime = $converted['mime_type'];
                    $size = $converted['size'];
                    $dimensions = $converted['dimensions'];
                } catch (\Throwable $e) {
                    Log::warning('WebP conversion fallback on upload: '.$e->getMessage());
                    $path = $file->storeAs('uploads/'.date('Y/m'), $safeName, 'public');
                    $this->mirrorStorageFile($path);
                    $mime = $file->getMimeType();
                    $size = $file->getSize();
                    $dimensions = null;
                    if (str_starts_with($mime, 'image/')) {
                        $imageInfo = @getimagesize($file->getRealPath());
                        if ($imageInfo) {
                            $dimensions = "{$imageInfo[0]}x{$imageInfo[1]}";
                        }
                    }
                }
            } else {
                $path = $file->storeAs('uploads/'.date('Y/m'), $safeName, 'public');
                $this->mirrorStorageFile($path);
                $mime = $file->getMimeType();
                $size = $file->getSize();
                $dimensions = null;
            }

            $mediaItem = Media::create([
                'name' => $originalName,
                'file_name' => $safeName,
                'file_path' => $path,
                'disk' => 'public',
                'mime_type' => $mime,
                'size' => $size,
                'dimensions' => $dimensions,
                'alt_text' => $originalName,
                'caption' => null,
                'user_id' => auth()->id(),
            ]);

            $uploadedFiles[] = $mediaItem;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles).' file(s) uploaded successfully.',
                'media' => $uploadedFiles,
                'location' => $uploadedFiles[0]->url ?? '', // for TinyMCE / Quill upload
            ]);
        }

        return redirect()->route('admin.media.index')
            ->with('success', count($uploadedFiles).' file(s) uploaded successfully.');
    }

    /**
     * Update media metadata (alt text, caption, name).
     */
    public function update(Request $request, Media $medium): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
        ]);

        $medium->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'media' => $medium]);
        }

        return redirect()->route('admin.media.index')
            ->with('success', 'Media information updated.');
    }

    /**
     * Delete media item.
     */
    public function destroy(Media $medium): RedirectResponse|JsonResponse
    {
        if (Storage::disk($medium->disk ?? 'public')->exists($medium->file_path)) {
            Storage::disk($medium->disk ?? 'public')->delete($medium->file_path);
        }

        $publicFile = public_path('storage/'.ltrim($medium->file_path, '/'));
        if (file_exists($publicFile)) {
            @unlink($publicFile);
        }

        $appStorageFile = storage_path('app/public/'.ltrim($medium->file_path, '/'));
        if (file_exists($appStorageFile)) {
            @unlink($appStorageFile);
        }

        $medium->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.media.index')
            ->with('success', 'Media file deleted successfully.');
    }

    /**
     * API endpoint returning recent media for the media picker modal.
     */
    public function modalList(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $media = Media::latest()
            ->when($search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%");
            })
            ->take(50)
            ->get();

        return response()->json($media);
    }

    /**
     * Mirror a stored file between public/storage and storage/app/public.
     */
    protected function mirrorStorageFile(string $path): void
    {
        $publicFile = public_path('storage/'.$path);
        $appStorageFile = storage_path('app/public/'.$path);

        if (file_exists($publicFile) && ! file_exists($appStorageFile)) {
            $dir = dirname($appStorageFile);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            @copy($publicFile, $appStorageFile);
        } elseif (file_exists($appStorageFile) && ! file_exists($publicFile)) {
            $dir = dirname($publicFile);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            @copy($appStorageFile, $publicFile);
        }
    }
}

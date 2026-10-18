<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use App\Services\WebpConverterService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DownloadAndConvertWebpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:download-convert-webp
                            {--limit= : Limit the number of remote images to download}
                            {--force : Force re-download and re-conversion of existing images}
                            {--skip-wp-media : Skip crawling the full WP Media Library API}
                            {--chunk=10 : Number of concurrent downloads}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download all images from lifeguarddatarecovery.com, convert them to WebP with reduced file size, store locally, and update all database references';

    /**
     * Service instance.
     */
    protected WebpConverterService $converter;

    /**
     * Map of remote/old URL => local WebP URL.
     */
    protected array $urlMap = [];

    /**
     * Metadata cache by URL for alt text and titles.
     */
    protected array $metaByUrl = [];

    /**
     * Fallback local WebP URL for 404 remote images.
     */
    protected ?string $fallbackWebpUrl = null;

    /**
     * Statistics.
     */
    protected int $totalFound = 0;

    protected int $convertedCount = 0;

    protected int $alreadyLocalCount = 0;

    protected int $failedCount = 0;

    protected int $totalOriginalBytes = 0;

    protected int $totalWebpBytes = 0;

    public function handle(WebpConverterService $converter): int
    {
        $this->converter = $converter;

        $this->info('========================================================================');
        $this->info('  LifeGuard Data Recovery - WebP Image Downloader & Optimizer');
        $this->info('========================================================================');

        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $force = (bool) $this->option('force');
        $skipWpMedia = (bool) $this->option('skip-wp-media');
        $chunkSize = max(1, min(25, (int) $this->option('chunk')));

        // Step 1: Discover all image URLs across DB and WP Media API
        $this->info("\n--> [Step 1/4] Discovering all images from WordPress and Database...");
        $uniqueUrls = $this->collectAllImageUrls($skipWpMedia);
        $this->totalFound = count($uniqueUrls);
        $this->info("  Found {$this->totalFound} unique images to process.");

        if ($limit && $limit < count($uniqueUrls)) {
            $uniqueUrls = array_slice($uniqueUrls, 0, $limit);
            $this->warn("  [Notice] Limited run to first {$limit} images.");
        }

        // Step 2: Download & Convert to WebP in concurrent batches
        $this->info("\n--> [Step 2/4] Downloading & Converting images to WebP (chunks of {$chunkSize})...");
        $this->processUrlsInBatches($uniqueUrls, $chunkSize, $force);

        // Step 3: Convert any local non-WebP service images
        $this->info("\n--> [Step 3/4] Checking and converting local service images to WebP...");
        $this->processLocalServiceImages();

        // Step 4: Rewrite Database References in Posts, Pages, Services
        $this->info("\n--> [Step 4/4] Updating database references to local WebP images...");
        $updates = $this->rewriteDatabaseReferences();

        // Summary Report
        $this->displaySummary($updates);

        return Command::SUCCESS;
    }

    /**
     * Process list of URLs using fast local checking + concurrent Http::pool downloads.
     */
    protected function processUrlsInBatches(array $urls, int $chunkSize, bool $force): void
    {
        $needsDownload = [];

        $bar = $this->output->createProgressBar(count($urls));
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $bar->start();

        // Check which files are already converted locally
        foreach ($urls as $url) {
            $shortName = basename(parse_url($url, PHP_URL_PATH) ?? $url);
            $bar->setMessage(Str::limit($shortName, 28));

            $localInfo = $this->resolveLocalTarget($url);
            if (! $force && file_exists($localInfo['appPath']) && filesize($localInfo['appPath']) > 100) {
                if (! file_exists($localInfo['publicPath'])) {
                    $dir = dirname($localInfo['publicPath']);
                    if (! is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    @copy($localInfo['appPath'], $localInfo['publicPath']);
                }

                $this->alreadyLocalCount++;
                $this->recordUrlMapping($url, $localInfo['url']);
                $this->syncMediaRecord($localInfo['relativePath'], $localInfo['fileName'], $url);

                if (! $this->fallbackWebpUrl) {
                    $this->fallbackWebpUrl = $localInfo['url'];
                }
            } else {
                $needsDownload[] = $url;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if (empty($needsDownload)) {
            $this->info("  All {$this->alreadyLocalCount} images are already converted to WebP locally.");

            return;
        }

        $this->info('  Downloading '.count($needsDownload)." remaining images in batches of {$chunkSize}...");
        $downloadBar = $this->output->createProgressBar(count($needsDownload));
        $downloadBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $downloadBar->start();

        $chunks = array_chunk($needsDownload, $chunkSize);
        foreach ($chunks as $chunk) {
            $responses = Http::pool(function (Pool $pool) use ($chunk) {
                return array_map(function ($url) use ($pool) {
                    return $pool->withoutVerifying()
                        ->timeout(25)
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
                            'Accept' => 'image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
                            'Referer' => 'https://lifeguarddatarecovery.com/',
                        ])
                        ->get($url);
                }, $chunk);
            });

            foreach ($chunk as $idx => $url) {
                $response = $responses[$idx] ?? null;
                $shortName = basename(parse_url($url, PHP_URL_PATH) ?? $url);
                $downloadBar->setMessage(Str::limit($shortName, 28));

                if ($response instanceof Response && $response->successful() && strlen($response->body()) >= 64) {
                    $binary = $response->body();
                    $origSize = strlen($binary);
                    $this->totalOriginalBytes += $origSize;

                    $localInfo = $this->resolveLocalTarget($url);
                    $converted = $this->converter->convertBinary(
                        binary: $binary,
                        preferredName: $localInfo['fileName'],
                        subfolder: $localInfo['subfolder'],
                        quality: 80,
                        maxWidth: 2048
                    );

                    if ($converted) {
                        $this->convertedCount++;
                        $this->totalWebpBytes += $converted['size'];
                        $this->recordUrlMapping($url, $converted['url']);
                        $this->syncMediaRecord($converted['file_path'], $converted['file_name'], $url, $converted);

                        if (! $this->fallbackWebpUrl) {
                            $this->fallbackWebpUrl = $converted['url'];
                        }
                    } else {
                        $this->failedCount++;
                    }
                } else {
                    $this->failedCount++;
                }

                $downloadBar->advance();
            }
        }

        $downloadBar->finish();
        $this->newLine();
    }

    /**
     * Compute local paths and subfolders from a URL.
     */
    protected function resolveLocalTarget(string $url): array
    {
        $parsedPath = parse_url($url, PHP_URL_PATH) ?? '';
        $basename = basename($parsedPath);
        $filenameWithoutExt = pathinfo($basename, PATHINFO_FILENAME);
        $safeBase = Str::slug($filenameWithoutExt);
        if (empty($safeBase)) {
            $safeBase = 'media-'.Str::random(8);
        }

        if (preg_match('#wp-content/uploads/(\d{4}/\d{2})#i', $parsedPath, $m)) {
            $subfolder = 'media/'.$m[1];
        } elseif (preg_match('#wp-content/uploads/elementor/thumbs#i', $parsedPath)) {
            $subfolder = 'media/thumbs';
        } elseif (preg_match('#wp-content/uploads/(\d{4})#i', $parsedPath, $m)) {
            $subfolder = 'media/'.$m[1];
        } else {
            $subfolder = 'media/'.date('Y/m');
        }

        $fileName = $safeBase.'.webp';
        $relativePath = trim($subfolder, '/').'/'.$fileName;

        return [
            'subfolder' => $subfolder,
            'fileName' => $fileName,
            'relativePath' => $relativePath,
            'appPath' => storage_path('app/public/'.$relativePath),
            'publicPath' => public_path('storage/'.$relativePath),
            'url' => '/storage/'.$relativePath,
        ];
    }

    /**
     * Collect all image URLs from posts, pages, services, content regex, and WP Media API.
     */
    protected function collectAllImageUrls(bool $skipWpMedia): array
    {
        $urls = [];

        // 1. Featured images in Posts
        foreach (Post::whereNotNull('featured_image')->where('featured_image', '<>', '')->pluck('featured_image') as $img) {
            $this->registerCandidateUrl($img, $urls);
        }

        // 2. Featured images in Pages
        foreach (Page::whereNotNull('featured_image')->where('featured_image', '<>', '')->pluck('featured_image') as $img) {
            $this->registerCandidateUrl($img, $urls);
        }

        // 3. Featured images in Services
        foreach (Service::whereNotNull('featured_image')->where('featured_image', '<>', '')->pluck('featured_image') as $img) {
            $this->registerCandidateUrl($img, $urls);
        }

        // 4. Inline <img> in Posts content
        foreach (Post::whereNotNull('content')->cursor() as $post) {
            if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $post->content, $matches)) {
                foreach ($matches[1] as $src) {
                    $this->registerCandidateUrl($src, $urls);
                }
            }
            if (preg_match_all('/srcset=["\']([^"\']+)["\']/i', $post->content, $matches)) {
                foreach ($matches[1] as $srcset) {
                    foreach (explode(',', $srcset) as $part) {
                        $src = trim(explode(' ', trim($part))[0]);
                        $this->registerCandidateUrl($src, $urls);
                    }
                }
            }
        }

        // 5. Inline <img> in Pages content
        foreach (Page::whereNotNull('content')->cursor() as $page) {
            if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $page->content, $matches)) {
                foreach ($matches[1] as $src) {
                    $this->registerCandidateUrl($src, $urls);
                }
            }
            if (preg_match_all('/srcset=["\']([^"\']+)["\']/i', $page->content, $matches)) {
                foreach ($matches[1] as $srcset) {
                    foreach (explode(',', $srcset) as $part) {
                        $src = trim(explode(' ', trim($part))[0]);
                        $this->registerCandidateUrl($src, $urls);
                    }
                }
            }
        }

        // 6. WordPress REST Media Library API
        if (! $skipWpMedia) {
            $this->fetchWpMediaLibrary($urls);
        }

        return array_values($urls);
    }

    /**
     * Crawl the WordPress REST Media API across all pages.
     */
    protected function fetchWpMediaLibrary(array &$urls): void
    {
        $this->line('  Querying WordPress Media REST API (https://lifeguarddatarecovery.com/wp-json/wp/v2/media)...');
        $page = 1;
        $maxPages = 20;

        while ($page <= $maxPages) {
            try {
                $response = Http::timeout(20)
                    ->withoutVerifying()
                    ->get('https://lifeguarddatarecovery.com/wp-json/wp/v2/media', [
                        'per_page' => 100,
                        'page' => $page,
                    ]);

                if (! $response->successful()) {
                    break;
                }

                $totalPagesHeader = (int) ($response->header('X-WP-TotalPages') ?? 1);
                $items = $response->json();

                if (empty($items) || ! is_array($items)) {
                    break;
                }

                foreach ($items as $item) {
                    $sourceUrl = $item['source_url'] ?? null;
                    if ($sourceUrl) {
                        $clean = $this->cleanCandidateUrl($sourceUrl);
                        if ($clean) {
                            $urls[$clean] = $clean;
                            $this->metaByUrl[$clean] = [
                                'title' => $item['title']['rendered'] ?? null,
                                'alt' => $item['alt_text'] ?? null,
                            ];
                        }
                    }

                    if (! empty($item['media_details']['sizes']) && is_array($item['media_details']['sizes'])) {
                        foreach ($item['media_details']['sizes'] as $sizeInfo) {
                            if (! empty($sizeInfo['source_url'])) {
                                $cleanSize = $this->cleanCandidateUrl($sizeInfo['source_url']);
                                if ($cleanSize) {
                                    $urls[$cleanSize] = $cleanSize;
                                    $this->metaByUrl[$cleanSize] = [
                                        'title' => $item['title']['rendered'] ?? null,
                                        'alt' => $item['alt_text'] ?? null,
                                    ];
                                }
                            }
                        }
                    }
                }

                $page++;
                if ($page > $totalPagesHeader) {
                    break;
                }
            } catch (\Throwable $e) {
                $this->warn("  WP Media API fetch stopped at page {$page}: ".$e->getMessage());
                break;
            }
        }
    }

    /**
     * Clean and register a candidate URL.
     */
    protected function registerCandidateUrl(string $rawUrl, array &$urls): void
    {
        $clean = $this->cleanCandidateUrl($rawUrl);
        if ($clean) {
            $urls[$clean] = $clean;
        }
    }

    /**
     * Normalize URL, ensure it points to lifeguarddatarecovery.com wp-content.
     */
    protected function cleanCandidateUrl(string $url): ?string
    {
        $url = trim($url);
        if (empty($url) || str_starts_with($url, 'data:')) {
            return null;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            if (! str_contains($url, 'lifeguarddatarecovery.com')) {
                return null;
            }
        } elseif (str_starts_with($url, '/wp-content/') || str_starts_with($url, 'wp-content/')) {
            $url = 'https://lifeguarddatarecovery.com/'.ltrim($url, '/');
        } else {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'avif'], true)) {
            return null;
        }

        return strtok($url, '?');
    }

    /**
     * Map old remote URL to new local WebP URL (including relative variants).
     */
    protected function recordUrlMapping(string $remoteUrl, string $localUrl): void
    {
        $this->urlMap[$remoteUrl] = $localUrl;

        if (str_starts_with($remoteUrl, 'https://')) {
            $this->urlMap[str_replace('https://', 'http://', $remoteUrl)] = $localUrl;
        }

        $parsedPath = parse_url($remoteUrl, PHP_URL_PATH);
        if ($parsedPath) {
            $this->urlMap[$parsedPath] = $localUrl;
            $this->urlMap[ltrim($parsedPath, '/')] = $localUrl;

            $decodedPath = urldecode($parsedPath);
            if ($decodedPath !== $parsedPath) {
                $this->urlMap[$decodedPath] = $localUrl;
                $this->urlMap[ltrim($decodedPath, '/')] = $localUrl;
            }
        }
    }

    /**
     * Sync or create Media record in database for the WebP image.
     */
    protected function syncMediaRecord(string $filePath, string $fileName, string $sourceUrl, ?array $converted = null): void
    {
        $meta = $this->metaByUrl[$sourceUrl] ?? [];
        $title = $meta['title'] ?? pathinfo($fileName, PATHINFO_FILENAME);
        $alt = $meta['alt'] ?? $title;

        $absPath = storage_path('app/public/'.$filePath);
        $size = $converted['size'] ?? (file_exists($absPath) ? filesize($absPath) : 0);
        $dimensions = $converted['dimensions'] ?? null;

        if (! $dimensions && file_exists($absPath)) {
            $info = @getimagesize($absPath);
            if ($info) {
                $dimensions = "{$info[0]}x{$info[1]}";
            }
        }

        Media::updateOrCreate(
            ['file_path' => $filePath],
            [
                'name' => Str::limit($title, 250, ''),
                'file_name' => $fileName,
                'disk' => 'public',
                'mime_type' => 'image/webp',
                'size' => $size,
                'dimensions' => $dimensions,
                'alt_text' => Str::limit($alt, 250, ''),
                'caption' => null,
                'user_id' => 1,
            ]
        );
    }

    /**
     * Process existing local service images that are still .jpg or .png.
     */
    protected function processLocalServiceImages(): void
    {
        $services = Service::whereNotNull('featured_image')->get();

        foreach ($services as $service) {
            $currentImage = $service->featured_image;
            if (empty($currentImage) || str_ends_with(strtolower($currentImage), '.webp')) {
                continue;
            }

            $cleanPath = ltrim(str_replace('/storage/', '', $currentImage), '/');
            $absPath = public_path('storage/'.$cleanPath);
            if (! file_exists($absPath)) {
                $absPath = storage_path('app/public/'.$cleanPath);
            }

            if (file_exists($absPath)) {
                $targetRelative = pathinfo($cleanPath, PATHINFO_DIRNAME).'/'.pathinfo($cleanPath, PATHINFO_FILENAME).'.webp';
                $converted = $this->converter->convertLocalFile(
                    absoluteSourcePath: $absPath,
                    targetRelativePath: $targetRelative,
                    quality: 80
                );

                if ($converted) {
                    $service->update(['featured_image' => $targetRelative]);
                    $this->recordUrlMapping($currentImage, '/storage/'.$targetRelative);
                    $this->recordUrlMapping('/storage/'.$cleanPath, '/storage/'.$targetRelative);
                    $this->line("  [Service Converted] {$service->title} -> {$targetRelative}");
                }
            }
        }
    }

    /**
     * Replace all remote / old image URLs in posts, pages, and services with local WebP URLs.
     */
    protected function rewriteDatabaseReferences(): array
    {
        if (empty($this->urlMap)) {
            return ['posts' => 0, 'pages' => 0, 'services' => 0];
        }

        $postsUpdated = 0;
        $pagesUpdated = 0;
        $servicesUpdated = 0;

        uksort($this->urlMap, fn ($a, $b) => strlen($b) <=> strlen($a));
        $searchKeys = array_keys($this->urlMap);
        $replaceValues = array_values($this->urlMap);

        // 1. Update Posts
        foreach (Post::cursor() as $post) {
            $dirty = false;

            if (! empty($post->featured_image)) {
                if (isset($this->urlMap[$post->featured_image])) {
                    $post->featured_image = $this->urlMap[$post->featured_image];
                    $dirty = true;
                } elseif (str_contains($post->featured_image, 'lifeguarddatarecovery.com') && $this->fallbackWebpUrl) {
                    // Remote image that failed (404 on WP) -> point to reliable local WebP
                    $post->featured_image = $this->fallbackWebpUrl;
                    $dirty = true;
                }
            }

            if (! empty($post->og_image)) {
                if (isset($this->urlMap[$post->og_image])) {
                    $post->og_image = $this->urlMap[$post->og_image];
                    $dirty = true;
                } elseif (str_contains($post->og_image, 'lifeguarddatarecovery.com') && $this->fallbackWebpUrl) {
                    $post->og_image = $this->fallbackWebpUrl;
                    $dirty = true;
                }
            }

            if (! empty($post->content)) {
                $newContent = str_replace($searchKeys, $replaceValues, $post->content);
                if ($newContent !== $post->content) {
                    $post->content = $newContent;
                    $dirty = true;
                }
            }

            if ($dirty) {
                $post->save();
                $postsUpdated++;
            }
        }

        // 2. Update Pages
        foreach (Page::cursor() as $page) {
            $dirty = false;

            if (! empty($page->featured_image)) {
                if (isset($this->urlMap[$page->featured_image])) {
                    $page->featured_image = $this->urlMap[$page->featured_image];
                    $dirty = true;
                } elseif (str_contains($page->featured_image, 'lifeguarddatarecovery.com') && $this->fallbackWebpUrl) {
                    $page->featured_image = $this->fallbackWebpUrl;
                    $dirty = true;
                }
            }

            if (! empty($page->og_image)) {
                if (isset($this->urlMap[$page->og_image])) {
                    $page->og_image = $this->urlMap[$page->og_image];
                    $dirty = true;
                } elseif (str_contains($page->og_image, 'lifeguarddatarecovery.com') && $this->fallbackWebpUrl) {
                    $page->og_image = $this->fallbackWebpUrl;
                    $dirty = true;
                }
            }

            if (! empty($page->content)) {
                $newContent = str_replace($searchKeys, $replaceValues, $page->content);
                if ($newContent !== $page->content) {
                    $page->content = $newContent;
                    $dirty = true;
                }
            }

            if ($dirty) {
                $page->save();
                $pagesUpdated++;
            }
        }

        // 3. Update Services
        foreach (Service::cursor() as $service) {
            $dirty = false;

            if (! empty($service->featured_image) && isset($this->urlMap[$service->featured_image])) {
                $service->featured_image = $this->urlMap[$service->featured_image];
                $dirty = true;
            }

            if (! empty($service->content)) {
                $newContent = str_replace($searchKeys, $replaceValues, $service->content);
                if ($newContent !== $service->content) {
                    $service->content = $newContent;
                    $dirty = true;
                }
            }

            if ($dirty) {
                $service->save();
                $servicesUpdated++;
            }
        }

        return [
            'posts' => $postsUpdated,
            'pages' => $pagesUpdated,
            'services' => $servicesUpdated,
        ];
    }

    /**
     * Display final statistics and results.
     */
    protected function displaySummary(array $updates): void
    {
        $this->newLine();
        $this->info('========================================================================');
        $this->info('  WebP Conversion & Download Summary');
        $this->info('========================================================================');
        $this->table(
            ['Metric', 'Count / Value'],
            [
                ['Total Discovered Images', $this->totalFound],
                ['Downloaded & Converted to WebP', $this->convertedCount],
                ['Already Local / Reused', $this->alreadyLocalCount],
                ['Failed / 404 on WP', $this->failedCount],
                ['URL Mappings Active', count($this->urlMap)],
                ['Original Download Bytes', $this->formatBytes($this->totalOriginalBytes)],
                ['WebP Converted Bytes', $this->formatBytes($this->totalWebpBytes)],
                ['Total Space Saved', $this->formatBytes(max(0, $this->totalOriginalBytes - $this->totalWebpBytes))],
                ['Posts Rewritten', $updates['posts']],
                ['Pages Rewritten', $updates['pages']],
                ['Services Rewritten', $updates['services']],
                ['Media Library DB Items', Media::count()],
            ]
        );
        $this->info('========================================================================');
    }

    /**
     * Format bytes into human readable format.
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }
}

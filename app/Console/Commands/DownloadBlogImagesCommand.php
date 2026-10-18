<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Models\Post;
use App\Services\WebpConverterService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DownloadBlogImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:download-blog-images
                            {--force : Force re-download and re-conversion of existing images}
                            {--chunk=15 : Number of concurrent downloads}
                            {--limit= : Limit number of images to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download all blog images from lifeguarddatarecovery.com, convert them to WebP with reduced file size, and update all blog post records';

    /**
     * Service instance.
     */
    protected WebpConverterService $converter;

    /**
     * Map of remote URL => local WebP URL.
     */
    protected array $urlMap = [];

    /**
     * Fallback WebP URL for 404 images.
     */
    protected ?string $fallbackWebpUrl = null;

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
        $this->info('  LifeGuard Data Recovery - Blog Images Downloader & WebP Converter');
        $this->info('========================================================================');

        $force = (bool) $this->option('force');
        $chunkSize = max(1, min(25, (int) $this->option('chunk')));
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        // Step 1: Collect all distinct blog image URLs from Posts
        $this->info("\n--> [Step 1/3] Collecting all image URLs from 396 blog posts...");
        $uniqueUrls = $this->collectBlogImageUrls();
        $this->totalFound = count($uniqueUrls);
        $this->info("  Found {$this->totalFound} distinct image URLs across all blog posts.");

        if ($limit && $limit < count($uniqueUrls)) {
            $uniqueUrls = array_slice($uniqueUrls, 0, $limit);
            $this->warn("  [Notice] Limited run to {$limit} images.");
        }

        // Step 2: Download & Convert to WebP
        $this->info("\n--> [Step 2/3] Downloading & Converting blog images to WebP (batch size: {$chunkSize})...");
        $this->processBlogImagesInBatches($uniqueUrls, $chunkSize, $force);

        // Step 3: Rewrite Database References in Posts
        $this->info("\n--> [Step 3/3] Updating blog posts with local WebP images...");
        $postsUpdated = $this->rewriteBlogPosts();

        // Summary
        $this->displaySummary($postsUpdated);

        return Command::SUCCESS;
    }

    /**
     * Collect all image URLs from posts table (featured_image, og_image, inline HTML).
     */
    protected function collectBlogImageUrls(): array
    {
        $urls = [];

        foreach (Post::cursor() as $post) {
            // Featured Image
            if (! empty($post->featured_image)) {
                $this->addCandidateUrl($post->featured_image, $urls);
            }

            // OG Image
            if (! empty($post->og_image)) {
                $this->addCandidateUrl($post->og_image, $urls);
            }

            // Inline Images in content
            if (! empty($post->content)) {
                if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $post->content, $matches)) {
                    foreach ($matches[1] as $src) {
                        $this->addCandidateUrl($src, $urls);
                    }
                }
                if (preg_match_all('/srcset=["\']([^"\']+)["\']/i', $post->content, $matches)) {
                    foreach ($matches[1] as $srcset) {
                        foreach (explode(',', $srcset) as $part) {
                            $src = trim(explode(' ', trim($part))[0]);
                            $this->addCandidateUrl($src, $urls);
                        }
                    }
                }
            }
        }

        return array_values($urls);
    }

    /**
     * Validate, clean, and add candidate URL.
     */
    protected function addCandidateUrl(string $rawUrl, array &$urls): void
    {
        $url = trim($rawUrl);
        if (empty($url) || str_starts_with($url, 'data:')) {
            return;
        }

        // Ignore already local /storage/media/ URLs unless needing verification
        if (str_starts_with($url, '/storage/') || str_starts_with($url, 'storage/')) {
            $cleanRel = ltrim(str_replace('/storage/', '', $url), '/');
            if (str_ends_with(strtolower($cleanRel), '.webp')) {
                // If it's already a local webp, store in urlMap directly
                $this->urlMap[$url] = '/storage/'.$cleanRel;
                if (! $this->fallbackWebpUrl) {
                    $this->fallbackWebpUrl = '/storage/'.$cleanRel;
                }

                return;
            }
        }

        // Keep lifeguarddatarecovery.com images
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            if (! str_contains($url, 'lifeguarddatarecovery.com')) {
                return;
            }
        } elseif (str_starts_with($url, '/wp-content/') || str_starts_with($url, 'wp-content/')) {
            $url = 'https://lifeguarddatarecovery.com/'.ltrim($url, '/');
        } else {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'avif'], true)) {
            return;
        }

        $cleanUrl = strtok($url, '?');
        $urls[$cleanUrl] = $cleanUrl;
    }

    /**
     * Download and convert images in concurrent batches.
     */
    protected function processBlogImagesInBatches(array $urls, int $chunkSize, bool $force): void
    {
        $needsDownload = [];

        // Check local existence first
        foreach ($urls as $url) {
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
        }

        $this->info("  Already locally available: {$this->alreadyLocalCount}");
        $this->info('  To download & convert: '.count($needsDownload));

        if (empty($needsDownload)) {
            return;
        }

        $bar = $this->output->createProgressBar(count($needsDownload));
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $bar->start();

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
                $bar->setMessage(Str::limit($shortName, 28));

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

                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Compute target folder and file names from URL.
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
     * Record mappings between remote URL variants and local WebP URL.
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
     * Sync Media model record.
     */
    protected function syncMediaRecord(string $filePath, string $fileName, string $sourceUrl, ?array $converted = null): void
    {
        $absPath = storage_path('app/public/'.$filePath);
        $size = $converted['size'] ?? (file_exists($absPath) ? filesize($absPath) : 0);
        $dimensions = $converted['dimensions'] ?? null;

        if (! $dimensions && file_exists($absPath)) {
            $info = @getimagesize($absPath);
            if ($info) {
                $dimensions = "{$info[0]}x{$info[1]}";
            }
        }

        $title = pathinfo($fileName, PATHINFO_FILENAME);

        Media::updateOrCreate(
            ['file_path' => $filePath],
            [
                'name' => Str::limit($title, 250, ''),
                'file_name' => $fileName,
                'disk' => 'public',
                'mime_type' => 'image/webp',
                'size' => $size,
                'dimensions' => $dimensions,
                'alt_text' => Str::limit($title, 250, ''),
                'user_id' => 1,
            ]
        );
    }

    /**
     * Rewrite database references in all blog posts.
     */
    protected function rewriteBlogPosts(): int
    {
        $postsUpdated = 0;

        if (! empty($this->urlMap)) {
            uksort($this->urlMap, fn ($a, $b) => strlen($b) <=> strlen($a));
            $searchKeys = array_keys($this->urlMap);
            $replaceValues = array_values($this->urlMap);
        } else {
            $searchKeys = [];
            $replaceValues = [];
        }

        foreach (Post::cursor() as $post) {
            $dirty = false;

            // 1. Featured Image
            if (! empty($post->featured_image)) {
                if (isset($this->urlMap[$post->featured_image])) {
                    $post->featured_image = $this->urlMap[$post->featured_image];
                    $dirty = true;
                } elseif (str_contains($post->featured_image, 'lifeguarddatarecovery.com') && $this->fallbackWebpUrl) {
                    $post->featured_image = $this->fallbackWebpUrl;
                    $dirty = true;
                }
            } elseif ($this->fallbackWebpUrl) {
                $post->featured_image = $this->fallbackWebpUrl;
                $dirty = true;
            }

            // 2. OG Image
            if (! empty($post->og_image)) {
                if (isset($this->urlMap[$post->og_image])) {
                    $post->og_image = $this->urlMap[$post->og_image];
                    $dirty = true;
                } elseif (str_contains($post->og_image, 'lifeguarddatarecovery.com') && $this->fallbackWebpUrl) {
                    $post->og_image = $this->fallbackWebpUrl;
                    $dirty = true;
                }
            }

            // 3. Content HTML
            if (! empty($post->content) && ! empty($searchKeys)) {
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

        return $postsUpdated;
    }

    /**
     * Display summary metrics.
     */
    protected function displaySummary(int $postsUpdated): void
    {
        $this->newLine();
        $this->info('========================================================================');
        $this->info('  Blog Images Downloader & WebP Optimizer Summary');
        $this->info('========================================================================');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Distinct Blog Images', $this->totalFound],
                ['Downloaded & Converted to WebP', $this->convertedCount],
                ['Already Converted / Local', $this->alreadyLocalCount],
                ['Failed / 404 on Remote Server', $this->failedCount],
                ['Active URL Mappings', count($this->urlMap)],
                ['Original Download Bytes', $this->formatBytes($this->totalOriginalBytes)],
                ['Optimized WebP Bytes', $this->formatBytes($this->totalWebpBytes)],
                ['Storage Space Saved', $this->formatBytes(max(0, $this->totalOriginalBytes - $this->totalWebpBytes))],
                ['Blog Posts Updated', $postsUpdated],
                ['Total Media Records in DB', Media::count()],
            ]
        );
        $this->info('========================================================================');
    }

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

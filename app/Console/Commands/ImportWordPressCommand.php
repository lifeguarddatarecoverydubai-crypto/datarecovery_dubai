<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Media;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\RedirectRule;
use App\Models\Service;
use App\Models\Tag;
use App\Models\User;
use App\Support\SeoTitleNormalizer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportWordPressCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:import-wordpress
                            {--url=https://lifeguarddatarecovery.com : The WordPress website URL}
                            {--truncate : Truncate posts, pages, categories, tags before import}
                            {--download-images : Download featured images to local storage}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import posts, categories, tags, pages, media, schema, and navigation from LifeGuard Data Recovery WordPress REST API';

    /**
     * Cache for user ID mapping: WP User ID => Laravel User ID.
     */
    protected array $userMap = [];

    /**
     * Cache for category ID mapping: WP Cat ID => Laravel Cat ID.
     */
    protected array $categoryMap = [];

    /**
     * Cache for tag ID mapping: WP Tag ID => Laravel Tag ID.
     */
    protected array $tagMap = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $baseUrl = rtrim($this->option('url'), '/');
        $this->info('=================================================');
        $this->info("  WordPress Importer for {$baseUrl}");
        $this->info("=================================================\n");

        if ($this->option('truncate')) {
            $this->warn('Truncating existing posts, pages, categories, tags, and menu items...');
            Post::query()->delete();
            Page::query()->delete();
            Category::query()->delete();
            Tag::query()->delete();
            MenuItem::query()->delete();
        }

        // 1. Import Users / Authors
        $this->importUsers($baseUrl);

        // 2. Import Categories
        $this->importCategories($baseUrl);

        // 3. Import Tags
        $this->importTags($baseUrl);

        // 4. Import Pages
        $this->importPages($baseUrl);

        // 5. Import Posts
        $this->importPosts($baseUrl);

        // 6. Setup Services
        $this->setupServices();

        // 7. Setup Navigation Menu
        $this->setupNavigationMenu();

        // 8. Setup Essential 301 Redirects
        $this->setupEssentialRedirects();

        $this->info("\n=================================================");
        $this->info('  WordPress Migration Completed Successfully!   ');
        $this->info('=================================================');
        $this->table(
            ['Entity', 'Count in Laravel'],
            [
                ['Posts', Post::count()],
                ['Published Posts', Post::where('status', 'published')->count()],
                ['Categories', Category::count()],
                ['Tags', Tag::count()],
                ['Pages', Page::count()],
                ['Services', Service::count()],
                ['Users', User::count()],
                ['Menu Items', MenuItem::count()],
                ['Redirect Rules', RedirectRule::count()],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Helper to fetch JSON from WP REST API with retry and timeout handling.
     */
    protected function fetchApi(string $url, array $params = []): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) LifeGuard-Migrator/2.0',
                'Accept' => 'application/json',
            ])->timeout(45)->withoutVerifying()->get($url, $params);

            if ($response->successful()) {
                return $response->json();
            }

            $this->warn("HTTP status {$response->status()} for {$url}");

            return null;
        } catch (\Throwable $e) {
            $this->error("API request failed: {$e->getMessage()} on {$url}");

            return null;
        }
    }

    /**
     * Import users / authors from WordPress.
     */
    protected function importUsers(string $baseUrl): void
    {
        $this->info('--> Setting up authors and users...');

        // Primary Super Admin
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'LifeGuard Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
        $this->userMap[1] = $adminUser->id;
        $this->userMap[0] = $adminUser->id;

        // Author 1: Muskan Dubai (Known author from sitemap)
        $muskan = User::firstOrCreate(
            ['email' => 'muskan@lifeguarddatarecovery.com'],
            [
                'name' => 'Muskan Dubai',
                'password' => Hash::make('password'),
                'role' => 'editor',
                'is_active' => true,
            ]
        );
        $this->userMap[2] = $muskan->id;

        // Author 2: Dubai LifeGuard
        $dubaiLg = User::firstOrCreate(
            ['email' => 'expert@lifeguarddatarecovery.com'],
            [
                'name' => 'LifeGuard Data Recovery Expert',
                'password' => Hash::make('password'),
                'role' => 'editor',
                'is_active' => true,
            ]
        );
        $this->userMap[3] = $dubaiLg->id;

        // Try fetching /wp/v2/users if public
        $wpUsers = $this->fetchApi("{$baseUrl}/wp-json/wp/v2/users");
        if (! empty($wpUsers) && is_array($wpUsers)) {
            foreach ($wpUsers as $u) {
                $slug = $u['slug'] ?? 'user-'.$u['id'];
                $name = $u['name'] ?? 'Author '.$u['id'];
                $email = $slug === 'admin' ? 'admin@example.com' : "{$slug}@lifeguarddatarecovery.com";

                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => Hash::make('password'),
                        'role' => $slug === 'admin' ? 'admin' : 'editor',
                        'is_active' => true,
                    ]
                );

                $this->userMap[$u['id']] = $user->id;
                $this->line("  [User] '{$name}' -> ID: {$user->id}");
            }
        }
    }

    /**
     * Import all categories.
     */
    protected function importCategories(string $baseUrl): void
    {
        $this->info('--> Fetching categories from WP...');
        $page = 1;
        $totalCats = 0;

        while (true) {
            $items = $this->fetchApi("{$baseUrl}/wp-json/wp/v2/categories", [
                'per_page' => 100,
                'page' => $page,
            ]);

            if (empty($items) || ! is_array($items)) {
                break;
            }

            foreach ($items as $c) {
                $name = html_entity_decode($c['name'] ?? 'Category '.$c['id']);
                $slug = Str::slug($c['slug'] ?? $name);

                $category = Category::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => Str::limit($name, 250, ''),
                        'description' => $c['description'] ?: null,
                    ]
                );

                $this->categoryMap[$c['id']] = $category->id;
                $totalCats++;
            }

            if (count($items) < 100) {
                break;
            }
            $page++;
        }

        $this->info("  Total Categories Imported/Synced: {$totalCats}");
    }

    /**
     * Import all tags.
     */
    protected function importTags(string $baseUrl): void
    {
        $this->info('--> Fetching tags from WP...');
        $page = 1;
        $totalTags = 0;

        while (true) {
            $items = $this->fetchApi("{$baseUrl}/wp-json/wp/v2/tags", [
                'per_page' => 100,
                'page' => $page,
            ]);

            if (empty($items) || ! is_array($items)) {
                break;
            }

            foreach ($items as $t) {
                $rawName = html_entity_decode($t['name'] ?? 'Tag '.$t['id']);
                $name = Str::limit(trim($rawName), 250, '');
                $slug = Str::limit(trim($t['slug'] ?? Str::slug($name)), 250, '');

                $tag = Tag::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'description' => $t['description'] ?: null,
                    ]
                );

                $this->tagMap[$t['id']] = $tag->id;
                $totalTags++;
            }

            if (count($items) < 100) {
                break;
            }
            $page++;
        }

        $this->info("  Total Tags Imported/Synced: {$totalTags}");
    }

    /**
     * Import all pages.
     */
    protected function importPages(string $baseUrl): void
    {
        $this->info('--> Fetching pages from WP (103+ pages)...');
        $page = 1;
        $totalPages = 0;
        $defaultUserId = array_values($this->userMap)[0] ?? 1;

        while (true) {
            $items = $this->fetchApi("{$baseUrl}/wp-json/wp/v2/pages", [
                'per_page' => 100,
                'page' => $page,
                '_embed' => 1,
            ]);

            if (empty($items) || ! is_array($items)) {
                break;
            }

            foreach ($items as $p) {
                $title = $this->normalizeTitleCase(html_entity_decode($p['title']['rendered'] ?? 'Page '.$p['id']));
                $slug = $p['slug'] ?? Str::slug($title);
                $content = $p['content']['rendered'] ?? '';
                $status = ($p['status'] ?? 'publish') === 'publish' ? 'published' : 'draft';

                // Featured image
                $featuredImage = null;
                if (! empty($p['_embedded']['wp:featuredmedia'][0]['source_url'])) {
                    $featuredImage = $p['_embedded']['wp:featuredmedia'][0]['source_url'];
                } elseif (! empty($p['yoast_head_json']['og_image'][0]['url'])) {
                    $featuredImage = $p['yoast_head_json']['og_image'][0]['url'];
                }

                if ($featuredImage && $this->option('download-images')) {
                    $featuredImage = $this->downloadImage($featuredImage);
                }

                $metaTitle = $this->stripSiteSuffix($p['yoast_head_json']['title'] ?? $title);
                $metaDesc = $p['yoast_head_json']['description'] ?? null;
                $canonicalUrl = $p['yoast_head_json']['canonical'] ?? $p['link'] ?? null;
                $ogTitle = $p['yoast_head_json']['og_title'] ?? $title;
                $ogDesc = $p['yoast_head_json']['og_description'] ?? $metaDesc;
                $ogImage = $p['yoast_head_json']['og_image'][0]['url'] ?? $featuredImage;

                $schemaMarkup = null;
                if (! empty($p['yoast_head_json']['schema'])) {
                    $schemaMarkup = json_encode($p['yoast_head_json']['schema'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }

                $authorId = $this->userMap[$p['author'] ?? 0] ?? $defaultUserId;

                Page::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'user_id' => $authorId,
                        'title' => Str::limit($title, 250, ''),
                        'content' => $content,
                        'featured_image' => Str::limit($featuredImage ?? '', 250, ''),
                        'template' => 'default',
                        'status' => $status,
                        'meta_title' => Str::limit($metaTitle, 250, ''),
                        'meta_description' => $metaDesc,
                        'canonical_url' => Str::limit($canonicalUrl ?? '', 250, ''),
                        'og_title' => Str::limit($ogTitle, 250, ''),
                        'og_description' => $ogDesc,
                        'og_image' => Str::limit($ogImage ?? '', 250, ''),
                        'is_indexable' => true,
                        'schema_markup' => $schemaMarkup,
                    ]
                );

                // Register 301 redirect if page has parent path in WP permalink
                $rawLink = $p['link'] ?? '';
                $parsedPath = trim(parse_url($rawLink, PHP_URL_PATH) ?? '', '/');
                if (! empty($parsedPath) && $parsedPath !== $slug) {
                    RedirectRule::updateOrCreate(
                        ['source_url' => '/'.$parsedPath],
                        [
                            'target_url' => '/'.$slug,
                            'status_code' => 301,
                            'match_type' => 'exact',
                            'group' => 'wp-nested-pages',
                            'description' => "Redirect multi-segment WP page /{$parsedPath} to canonical /{$slug}",
                            'is_active' => true,
                        ]
                    );
                    // Also with trailing slash
                    RedirectRule::updateOrCreate(
                        ['source_url' => '/'.$parsedPath.'/'],
                        [
                            'target_url' => '/'.$slug,
                            'status_code' => 301,
                            'match_type' => 'exact',
                            'group' => 'wp-nested-pages',
                            'description' => "Redirect multi-segment WP page /{$parsedPath}/ to canonical /{$slug}",
                            'is_active' => true,
                        ]
                    );
                }

                $totalPages++;
                $this->line("  [Page] {$title} (/{$slug})");
            }

            if (count($items) < 100) {
                break;
            }
            $page++;
        }

        $this->info("  Total Pages Imported/Synced: {$totalPages}");
    }

    /**
     * Import all posts (396+ items).
     */
    protected function importPosts(string $baseUrl): void
    {
        $this->info('--> Fetching posts from WP (396+ articles)...');
        $page = 1;
        $totalPosts = 0;
        $defaultUserId = array_values($this->userMap)[0] ?? 1;

        while (true) {
            $this->line("  Fetching posts page {$page}...");
            $items = $this->fetchApi("{$baseUrl}/wp-json/wp/v2/posts", [
                'per_page' => 100,
                'page' => $page,
                '_embed' => 1,
            ]);

            if (empty($items) || ! is_array($items)) {
                break;
            }

            foreach ($items as $p) {
                $rawTitle = html_entity_decode($p['title']['rendered'] ?? 'Post '.$p['id']);
                $title = Str::limit($this->normalizeTitleCase(trim($rawTitle)), 250, '');
                $slug = Str::limit($p['slug'] ?? Str::slug($title), 250, '');
                $content = $p['content']['rendered'] ?? '';
                $excerpt = strip_tags(html_entity_decode($p['excerpt']['rendered'] ?? ''));
                $status = ($p['status'] ?? 'publish') === 'publish' ? 'published' : 'draft';
                $publishedAt = ! empty($p['date']) ? Carbon::parse($p['date']) : now();

                // Featured image
                $featuredImage = null;
                if (! empty($p['_embedded']['wp:featuredmedia'][0]['source_url'])) {
                    $featuredImage = $p['_embedded']['wp:featuredmedia'][0]['source_url'];
                } elseif (! empty($p['yoast_head_json']['og_image'][0]['url'])) {
                    $featuredImage = $p['yoast_head_json']['og_image'][0]['url'];
                }

                if ($featuredImage && $this->option('download-images')) {
                    $featuredImage = $this->downloadImage($featuredImage);
                }

                $metaTitle = $this->stripSiteSuffix($p['yoast_head_json']['title'] ?? $title);
                $metaDesc = $p['yoast_head_json']['description'] ?? Str::limit($excerpt, 160, '');
                $canonicalUrl = $p['yoast_head_json']['canonical'] ?? $p['link'] ?? null;
                $ogTitle = $p['yoast_head_json']['og_title'] ?? $title;
                $ogDesc = $p['yoast_head_json']['og_description'] ?? $metaDesc;
                $ogImage = $p['yoast_head_json']['og_image'][0]['url'] ?? $featuredImage;

                $schemaMarkup = null;
                if (! empty($p['yoast_head_json']['schema'])) {
                    $schemaMarkup = json_encode($p['yoast_head_json']['schema'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }

                // Map author dynamically from _embed if present
                $authorId = $defaultUserId;
                if (! empty($p['_embedded']['author'][0]['id'])) {
                    $wpAuthorId = $p['_embedded']['author'][0]['id'];
                    $wpAuthorName = $p['_embedded']['author'][0]['name'] ?? 'Author';
                    $wpAuthorSlug = $p['_embedded']['author'][0]['slug'] ?? Str::slug($wpAuthorName);

                    if (! isset($this->userMap[$wpAuthorId])) {
                        $authorUser = User::firstOrCreate(
                            ['email' => "{$wpAuthorSlug}@lifeguarddatarecovery.com"],
                            [
                                'name' => $wpAuthorName,
                                'password' => Hash::make('password'),
                                'role' => 'editor',
                                'is_active' => true,
                            ]
                        );
                        $this->userMap[$wpAuthorId] = $authorUser->id;
                    }
                    $authorId = $this->userMap[$wpAuthorId];
                }

                $post = Post::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'user_id' => $authorId,
                        'title' => $title,
                        'excerpt' => Str::limit($excerpt, 500, ''),
                        'content' => $content,
                        'featured_image' => Str::limit($featuredImage ?? '', 250, ''),
                        'status' => $status,
                        'published_at' => $publishedAt,
                        'meta_title' => Str::limit($metaTitle, 250, ''),
                        'meta_description' => $metaDesc,
                        'canonical_url' => Str::limit($canonicalUrl ?? '', 250, ''),
                        'og_title' => Str::limit($ogTitle, 250, ''),
                        'og_description' => $ogDesc,
                        'og_image' => Str::limit($ogImage ?? '', 250, ''),
                        'is_indexable' => true,
                        'schema_markup' => $schemaMarkup,
                    ]
                );

                // Sync categories
                if (! empty($p['categories']) && is_array($p['categories'])) {
                    $catIds = [];
                    foreach ($p['categories'] as $wpCatId) {
                        if (isset($this->categoryMap[$wpCatId])) {
                            $catIds[] = $this->categoryMap[$wpCatId];
                        }
                    }
                    if (! empty($catIds)) {
                        $post->categories()->sync($catIds);
                    }
                }

                // Sync tags
                if (! empty($p['tags']) && is_array($p['tags'])) {
                    $tagIds = [];
                    foreach ($p['tags'] as $wpTagId) {
                        if (isset($this->tagMap[$wpTagId])) {
                            $tagIds[] = $this->tagMap[$wpTagId];
                        }
                    }
                    if (! empty($tagIds)) {
                        $post->tags()->sync($tagIds);
                    }
                }

                if ($featuredImage) {
                    $this->registerMedia($featuredImage, $authorId);
                }

                $totalPosts++;
            }

            $this->info("    Processed {$totalPosts} posts so far...");

            if (count($items) < 100) {
                break;
            }
            $page++;
        }

        $this->info("  Total Posts Imported/Synced: {$totalPosts}");
    }

    /**
     * Strip a trailing site-name suffix Yoast already baked into the WP title.
     */
    protected function stripSiteSuffix(string $title): string
    {
        return SeoTitleNormalizer::stripSiteSuffix($title);
    }

    /**
     * Title-case a fully lowercase WP title while preserving known tech acronyms.
     */
    protected function normalizeTitleCase(string $title): string
    {
        return SeoTitleNormalizer::normalizeTitleCase($title);
    }

    /**
     * Download an image to local public storage and return the relative path.
     */
    protected function downloadImage(string $url): string
    {
        try {
            $parsed = parse_url($url);
            $filename = basename($parsed['path'] ?? 'img_'.uniqid().'.jpg');
            $subDir = 'media/'.date('Y/m');
            $targetDir = storage_path('app/public/'.$subDir);

            if (! File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true, true);
            }

            $localPath = $targetDir.'/'.$filename;
            $relativeStoragePath = $subDir.'/'.$filename;

            if (! File::exists($localPath)) {
                $response = Http::timeout(15)->withoutVerifying()->get($url);
                if ($response->successful()) {
                    File::put($localPath, $response->body());

                    return $relativeStoragePath;
                }
            } else {
                return $relativeStoragePath;
            }
        } catch (\Throwable $e) {
            // Silently fallback to URL
        }

        return $url;
    }

    /**
     * Register an image URL or local path into the media library table.
     */
    protected function registerMedia(string $path, int $userId): void
    {
        $fileName = basename($path);
        Media::firstOrCreate(
            ['file_path' => Str::limit($path, 250, '')],
            [
                'name' => Str::limit(pathinfo($fileName, PATHINFO_FILENAME), 250, ''),
                'file_name' => Str::limit($fileName, 250, ''),
                'disk' => 'public',
                'mime_type' => 'image/jpeg',
                'size' => 0,
                'user_id' => $userId,
            ]
        );
    }

    /**
     * Populate the services table with authentic LifeGuard Data Recovery services.
     */
    protected function setupServices(): void
    {
        $this->info('--> Configuring LifeGuard Data Recovery specialized services...');

        $services = [
            [
                'title' => 'Hard Drive Data Recovery Dubai',
                'slug' => 'hard-drive-data-recovery-dubai',
                'tagline' => 'Certified Class 100 Cleanroom mechanical & logical recovery for HDDs',
                'icon' => 'hard-drive',
                'summary' => 'Comprehensive data recovery for clicking, failing, dropped, corrupted, or unreadable desktop, laptop, and enterprise hard drives with certified Class 100 cleanroom technology.',
                'features' => ['Mechanical head & spindle assembly replacement', 'Bad sector cloning & firmware corruption repair', 'Water, fire, and drop physical shock recovery', 'Support for Western Digital, Seagate, Toshiba, Hitachi'],
                'benefits' => ['Free initial diagnostic and evaluation', 'No Data – No Charge policy', 'Strict confidentiality and NDA protection', 'High success rate over 98%'],
                'order' => 1,
                'is_featured' => true,
            ],
            [
                'title' => 'SSD Drive Data Recovery Dubai',
                'slug' => 'ssd-drive-data-recovery-in-dubai',
                'tagline' => 'Specialized NAND Flash chip-off and controller diagnostics',
                'icon' => 'cpu',
                'summary' => 'Expert recovery for solid state drives suffering from dead controller, firmware lock, damaged NAND memory, bad sectors, or sudden death across SATA, mSATA, and M.2 NVMe form factors.',
                'features' => ['Controller bypass and direct NAND reading', 'Firmware repair for Samsung, Kingston, Crucial, SanDisk', 'Recovery from 0GB capacity or RAW partition', 'BitLocker and hardware encryption recovery'],
                'benefits' => ['Fast turnaround for critical files', 'Proprietary SSD diagnostic hardware', 'Zero risk of further degradation', 'Certified flash memory engineers'],
                'order' => 2,
                'is_featured' => true,
            ],
            [
                'title' => 'RAID Data Recovery Dubai',
                'slug' => 'raid-data-recovery-dubai',
                'tagline' => 'Enterprise RAID 0, 1, 5, 6, 10, 50, 60 array reconstruction',
                'icon' => 'server',
                'summary' => 'Mission-critical enterprise data recovery for failed RAID arrays, virtualized environments, SAN/NAS enclosures, and corrupt striped volumes with 24/7 priority emergency service.',
                'features' => ['Virtual RAID parity recalculation & array re-striping', 'Multiple disk failure recovery (RAID 5 & 6)', 'Hardware controller malfunction bypass', 'VMware ESXi, Hyper-V, and Proxmox VM recovery'],
                'benefits' => ['Emergency 24/7 response team', 'Onsite and remote assessment available', 'Strict enterprise security & compliance', 'Zero risk of array corruption during rebuild'],
                'order' => 3,
                'is_featured' => true,
            ],
            [
                'title' => 'NAS Data Recovery Dubai',
                'slug' => 'nas-data-recovery-dubai',
                'tagline' => 'Synology, QNAP, Asustor, WD My Cloud network storage recovery',
                'icon' => 'cloud',
                'summary' => 'Full recovery solutions for network-attached storage devices after configuration loss, multiple drive failures, accidental firmware upgrades, or corrupted ext4/Btrfs filesystems.',
                'features' => ['Support for Synology SHR, QNAP QTS, and TrueNAS ZFS', 'Btrfs, ext4, XFS, and ZFS proprietary pool reconstruction', 'Degraded or crashed volume recovery', 'iSCSI LUN extraction'],
                'benefits' => ['Direct disk image reconstruction without original hardware', 'Complete folder structure preservation', 'No alteration to original NAS drives', 'Enterprise confidential data handling'],
                'order' => 4,
                'is_featured' => true,
            ],
            [
                'title' => 'Server Data Recovery Dubai',
                'slug' => 'server-data-recovery-dubai',
                'tagline' => 'Dell PowerEdge, HP ProLiant, IBM, Cisco UCS enterprise server repair',
                'icon' => 'database',
                'summary' => 'Emergency server data recovery for enterprise database servers, Exchange mailboxes, SQL databases, ERP systems, and crashed server clusters.',
                'features' => ['Emergency 24/7 turnaround with cleanroom access', 'Active Directory, MS SQL, Oracle database restoration', 'Virtual machine disaster recovery (VHDX, VMDK)', 'Hardware controller failure bypass'],
                'benefits' => ['Dedicated senior server forensic team', 'NDA and ISO data confidentiality guarantee', 'Remote diagnostic availability', 'Minimum business downtime guarantee'],
                'order' => 5,
                'is_featured' => true,
            ],
            [
                'title' => 'CCTV Footage Recovery Dubai',
                'slug' => 'cctv-footage-recovery-dubai',
                'tagline' => 'DVR & NVR surveillance security video footage restoration',
                'icon' => 'video',
                'summary' => 'Specialized forensic recovery of overwritten, formatted, deleted, or corrupted video files from CCTV surveillance systems (Hikvision, Dahua, Uniview, CP Plus, Samsung).',
                'features' => ['Proprietary CCTV filesystem reconstruction (DHFS, WFS, etc.)', 'Fragmented video stream stitching (H.264, H.265, MP4)', 'Overwritten and deleted timestamp search', 'Court-admissible forensic integrity verification'],
                'benefits' => ['Time-stamped exact footage retrieval', 'High recovery success for legal investigations', 'Clean video extraction without artifacts', 'Fast turnaround for urgent incident review'],
                'order' => 6,
                'is_featured' => false,
            ],
            [
                'title' => 'SD Card & USB Data Recovery',
                'slug' => 'sd-card-data-recovery',
                'tagline' => 'Bent, snapped, unrecognized, or corrupted flash cards & USB drives',
                'icon' => 'disc',
                'summary' => 'Precision recovery from snapped USB connectors, monolithic thumb drives, SD cards, and microSD cards using advanced micro-soldering and trace repair techniques.',
                'features' => ['Monolithic chip-off pinout probing & soldering', 'Broken USB connector and trace rebuilding', 'Controller fault and raw NAND memory decoding', 'Formatting and accidental file deletion rescue'],
                'benefits' => ['Affordable pricing with No Data No Charge', 'Quick turnaround for personal & student drives', 'Support for all USB brands (SanDisk, Kingston, Sony)', 'Safe preservation of intact original memory'],
                'order' => 7,
                'is_featured' => false,
            ],
            [
                'title' => 'iPhone & Mobile Data Recovery',
                'slug' => 'iphone-data-recovery',
                'tagline' => 'Water damage, dead motherboard, and logic board micro-soldering',
                'icon' => 'smartphone',
                'summary' => 'Advanced mobile data recovery for liquid-damaged, dropped, bent, or completely unresponsive iPhones and Android smartphones with chip-level motherboard restoration.',
                'features' => ['Motherboard short circuit detection & repair', 'Power management IC and CPU line reballing', 'Water damage corrosion ultrasonic cleaning', 'Photos, WhatsApp messages, contacts & notes recovery'],
                'benefits' => ['Device privacy guaranteed', 'Highest recovery rate for water-damaged phones', 'Focus on extracting critical files without resetting device', 'Free inspection and diagnostic report'],
                'order' => 8,
                'is_featured' => false,
            ],
            [
                'title' => 'Ransomware Data Recovery',
                'slug' => 'ransomware-data-recovery',
                'tagline' => 'Decryption assistance, shadow copy analysis & raw data carving',
                'icon' => 'shield-alert',
                'summary' => 'Assistance for businesses hit by ransomware attacks. We identify malware strains, decrypt available keys, restore shadow copies, and carve clean unencrypted database fragments.',
                'features' => ['Malware strain identification & threat analysis', 'Database carving for SQL, ERP, and accounting records', 'Volume shadow copy and snapshot extraction', 'Cleanroom forensic sandboxing to prevent reinfection'],
                'benefits' => ['Alternative to paying cyber extortion', 'Forensic timeline analysis for IT security audits', 'Comprehensive recovery of mission-critical databases', 'Strict confidentiality and security standards'],
                'order' => 9,
                'is_featured' => false,
            ],
            [
                'title' => 'MacBook & Mac Data Recovery',
                'slug' => 'mac-data-recovery',
                'tagline' => 'Apple Silicon M1/M2/M3 & Intel T2 logic board data retrieval',
                'icon' => 'laptop',
                'summary' => 'Specialized data recovery for MacBook Pro, MacBook Air, iMac, and Mac Mini logic boards with embedded SSD chips and Apple security coprocessors.',
                'features' => ['Apple Silicon solder-down NAND diagnostics', 'T2 chip security handshake bypass', 'Liquid damage logic board trace reconstruction', 'APFS encrypted container restoration'],
                'benefits' => ['Apple certified component repair', 'Fast turnaround for Dubai business professionals', 'No data lost during macOS kernel panic', 'High recovery rate on water-damaged MacBooks'],
                'order' => 10,
                'is_featured' => false,
            ],
        ];

        foreach ($services as $srv) {
            Service::updateOrCreate(
                ['slug' => $srv['slug']],
                array_merge($srv, [
                    'status' => 'published',
                    'meta_title' => $srv['title'].' | LifeGuard Data Recovery Dubai',
                    'meta_description' => Str::limit($srv['summary'], 160, ''),
                    'canonical_url' => 'https://lifeguarddatarecovery.com/'.$srv['slug'],
                    'is_indexable' => true,
                ])
            );
        }

        $this->info('  Configured '.count($services).' Data Recovery Services');
    }

    /**
     * Setup the authentic header navigation menu.
     */
    protected function setupNavigationMenu(): void
    {
        $this->info('--> Setting up navigation menu...');

        MenuItem::query()->delete();

        // 1. Home
        MenuItem::create([
            'title' => 'Home',
            'url' => '/',
            'order' => 1,
            'is_active' => true,
            'location' => 'header',
        ]);

        // 2. About Us
        MenuItem::create([
            'title' => 'About Us',
            'url' => '/about-us',
            'order' => 2,
            'is_active' => true,
            'location' => 'header',
        ]);

        // 3. Services (Parent)
        $servicesParent = MenuItem::create([
            'title' => 'Services',
            'url' => '/services',
            'order' => 3,
            'is_active' => true,
            'location' => 'header',
        ]);

        // Submenu items under Services
        $subServices = [
            ['title' => 'Hard Drive Recovery', 'url' => '/hard-drive-data-recovery-dubai'],
            ['title' => 'SSD Drive Recovery', 'url' => '/ssd-drive-data-recovery-in-dubai'],
            ['title' => 'RAID Data Recovery', 'url' => '/raid-data-recovery-dubai'],
            ['title' => 'NAS Data Recovery', 'url' => '/nas-data-recovery-dubai'],
            ['title' => 'Server Recovery', 'url' => '/server-data-recovery-dubai'],
            ['title' => 'CCTV Footage Recovery', 'url' => '/cctv-footage-recovery-dubai'],
            ['title' => 'SD Card / Flash Recovery', 'url' => '/sd-card-data-recovery'],
            ['title' => 'iPhone / Mobile Recovery', 'url' => '/iphone-data-recovery'],
            ['title' => 'MacBook Data Recovery', 'url' => '/mac-data-recovery'],
            ['title' => 'Ransomware Recovery', 'url' => '/ransomware-data-recovery'],
        ];

        $order = 1;
        foreach ($subServices as $sub) {
            MenuItem::create([
                'parent_id' => $servicesParent->id,
                'title' => $sub['title'],
                'url' => $sub['url'],
                'order' => $order++,
                'is_active' => true,
                'location' => 'header',
            ]);
        }

        // 4. Locations (Parent)
        $locationsParent = MenuItem::create([
            'title' => 'Locations',
            'url' => '/data-recovery-dubai',
            'order' => 4,
            'is_active' => true,
            'location' => 'header',
        ]);

        $subLocations = [
            ['title' => 'Dubai (Headquarters)', 'url' => '/data-recovery-dubai'],
            ['title' => 'Abu Dhabi', 'url' => '/data-recovery-abu-dhabi'],
            ['title' => 'Sharjah', 'url' => '/data-recovery-sharjah'],
            ['title' => 'Ajman', 'url' => '/data-recovery-ajman'],
            ['title' => 'Deira Dubai', 'url' => '/data-recovery-deira'],
            ['title' => 'Business Bay', 'url' => '/data-recovery-in-business-bay'],
            ['title' => 'Dubai Marina', 'url' => '/data-recovery-dubai-marina'],
            ['title' => 'JLT', 'url' => '/data-recovery-jumeirah-lake-tower'],
        ];

        $locOrder = 1;
        foreach ($subLocations as $sub) {
            MenuItem::create([
                'parent_id' => $locationsParent->id,
                'title' => $sub['title'],
                'url' => $sub['url'],
                'order' => $locOrder++,
                'is_active' => true,
                'location' => 'header',
            ]);
        }

        // 5. Cost Calculator
        MenuItem::create([
            'title' => 'Cost Calculator',
            'url' => '/data-recovery-rate-calculator',
            'order' => 5,
            'is_active' => true,
            'location' => 'header',
        ]);

        // 6. Blog
        MenuItem::create([
            'title' => 'Blog',
            'url' => '/blog',
            'order' => 6,
            'is_active' => true,
            'location' => 'header',
        ]);

        // 7. Contact Us
        MenuItem::create([
            'title' => 'Contact Us',
            'url' => '/contact',
            'order' => 7,
            'is_active' => true,
            'location' => 'header',
        ]);

        $this->info('  Header Navigation Menu configured with dropdown services and locations');
    }

    /**
     * Setup essential 301 Redirect Rules in redirect_rules table.
     */
    protected function setupEssentialRedirects(): void
    {
        $this->info('--> Seeding essential 301 redirects...');

        $redirects = [
            ['/about' => '/about-us', 'desc' => 'About alias to About Us'],
            ['/contact-us' => '/contact', 'desc' => 'Contact Us alias to Contact'],
            ['/home' => '/', 'desc' => 'Home alias to root'],
            ['/home-2' => '/', 'desc' => 'Home-2 alias to root'],
            ['/index.html' => '/', 'desc' => 'Index.html alias to root'],
            ['/index.php' => '/', 'desc' => 'Index.php alias to root'],
            ['/feed' => '/blog', 'desc' => 'RSS feed redirect to blog'],
            ['/feed/' => '/blog', 'desc' => 'RSS feed trailing slash redirect to blog'],
            ['/data-recovery-dubai/what-is-the-cost-of-data-recovery-in-dubai/' => '/what-is-the-cost-of-data-recovery-in-dubai', 'desc' => 'Clean sub-page URL redirect'],
            ['/data-recovery-dubai/what-is-the-cost-of-data-recovery-in-dubai' => '/what-is-the-cost-of-data-recovery-in-dubai', 'desc' => 'Clean sub-page URL redirect'],
            ['/data-recovery-case-studies/why-data-recovery-is-expensive/' => '/why-data-recovery-is-expensive', 'desc' => 'Clean case-study sub-page URL redirect'],
            ['/data-recovery-case-studies/why-data-recovery-is-expensive' => '/why-data-recovery-is-expensive', 'desc' => 'Clean case-study sub-page URL redirect'],
            ['/home-2/how-does-mechanical-hard-drive-recovery-work/' => '/how-does-mechanical-hard-drive-recovery-work', 'desc' => 'Clean home-2 sub-page URL redirect'],
            ['/home-2/how-does-mechanical-hard-drive-recovery-work' => '/how-does-mechanical-hard-drive-recovery-work', 'desc' => 'Clean home-2 sub-page URL redirect'],
        ];

        foreach ($redirects as $item) {
            foreach ($item as $source => $target) {
                if ($source === 'desc') {
                    continue;
                }
                RedirectRule::updateOrCreate(
                    ['source_url' => $source],
                    [
                        'target_url' => $target,
                        'status_code' => 301,
                        'match_type' => 'exact',
                        'group' => 'seo-permalinks',
                        'description' => $item['desc'] ?? 'SEO Canonical Redirect',
                        'is_active' => true,
                    ]
                );
            }
        }

        RedirectRule::clearCache();
        $this->info('  Essential 301 Redirect Rules configured and cache cleared');
    }
}

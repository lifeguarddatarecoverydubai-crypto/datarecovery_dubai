<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    /**
     * Excluded slugs for internal redirects or dedicated system routes.
     */
    protected const EXCLUDED_PAGE_SLUGS = [
        'home',
        'home-2',
        'recovery-shop',
        'recovery-shop-new',
        'thankyou',
        'thankyou-2',
    ];

    /**
     * Master XML sitemap containing all indexable pages, services, categories, and articles.
     */
    public function index(): Response
    {
        $xml = Cache::remember($this->getCacheKey('sitemap_master_xml'), 3600, function (): string {
            $entries = array_merge(
                $this->getPagesEntries(),
                $this->getServicesEntries(),
                $this->getCategoriesEntries(),
                $this->getPostsEntries(),
                $this->getAuthorsEntries()
            );

            return $this->buildUrlsetXml($entries);
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    /**
     * Master XML sitemap index referencing specialized sub-sitemaps (Yoast SEO compatible).
     */
    public function sitemapIndex(): Response
    {
        $xml = Cache::remember($this->getCacheKey('sitemap_index_xml'), 3600, function (): string {
            $sitemaps = [
                [
                    'loc' => $this->formatSitemapUrl(route('sitemap.posts')),
                    'lastmod' => Post::published()->latest('updated_at')->first()?->updated_at?->toAtomString() ?? now()->toAtomString(),
                ],
                [
                    'loc' => $this->formatSitemapUrl(route('sitemap.pages')),
                    'lastmod' => Page::where('status', 'published')->latest('updated_at')->first()?->updated_at?->toAtomString() ?? now()->toAtomString(),
                ],
                [
                    'loc' => $this->formatSitemapUrl(route('sitemap.services')),
                    'lastmod' => Service::published()->latest('updated_at')->first()?->updated_at?->toAtomString() ?? now()->toAtomString(),
                ],
                [
                    'loc' => $this->formatSitemapUrl(route('sitemap.categories')),
                    'lastmod' => Category::latest('updated_at')->first()?->updated_at?->toAtomString() ?? now()->toAtomString(),
                ],
                [
                    'loc' => $this->formatSitemapUrl('/author-sitemap.xml'),
                    'lastmod' => now()->toAtomString(),
                ],
            ];

            return $this->buildIndexXml($sitemaps);
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    /**
     * Dedicated XML sitemap for blog posts and technical articles (396+ posts).
     */
    public function posts(): Response
    {
        $xml = Cache::remember($this->getCacheKey('sitemap_posts_xml'), 3600, function (): string {
            return $this->buildUrlsetXml($this->getPostsEntries());
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    /**
     * Dedicated XML sitemap for static and CMS pages (103+ pages).
     */
    public function pages(): Response
    {
        $xml = Cache::remember($this->getCacheKey('sitemap_pages_xml'), 3600, function (): string {
            return $this->buildUrlsetXml($this->getPagesEntries());
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    /**
     * Dedicated XML sitemap for data recovery services.
     */
    public function services(): Response
    {
        $xml = Cache::remember($this->getCacheKey('sitemap_services_xml'), 3600, function (): string {
            return $this->buildUrlsetXml($this->getServicesEntries());
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    /**
     * Dedicated XML sitemap for categories.
     */
    public function categories(): Response
    {
        $xml = Cache::remember($this->getCacheKey('sitemap_categories_xml'), 3600, function (): string {
            return $this->buildUrlsetXml($this->getCategoriesEntries());
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    /**
     * Dedicated XML sitemap for authors.
     */
    public function authors(): Response
    {
        $xml = Cache::remember($this->getCacheKey('sitemap_authors_xml'), 3600, function (): string {
            return $this->buildUrlsetXml($this->getAuthorsEntries());
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    /**
     * Dynamic robots.txt response.
     */
    public function robots(): Response
    {
        $sitemapUrl = $this->formatSitemapUrl(route('sitemap'));
        $sitemapIndexUrl = $this->formatSitemapUrl(route('sitemap.index'));
        $sitemapPagesUrl = $this->formatSitemapUrl(route('sitemap.pages'));
        $sitemapServicesUrl = $this->formatSitemapUrl(route('sitemap.services'));
        $sitemapPostsUrl = $this->formatSitemapUrl(route('sitemap.posts'));
        $sitemapCategoriesUrl = $this->formatSitemapUrl(route('sitemap.categories'));

        $content = <<<ROBOTS
# robots.txt for LifeGuard Data Recovery Dubai
# Website: https://lifeguarddatarecovery.com

User-agent: *
Allow: /
Allow: /images/
Allow: /storage/
Allow: /build/
Allow: /css/
Allow: /js/
Disallow: /admin
Disallow: /admin/
Disallow: /login
Disallow: /login/
Disallow: /register
Disallow: /register/
Disallow: /password/
Disallow: /api/
Disallow: /storage/framework/

# Sitemaps
Sitemap: {$sitemapIndexUrl}
Sitemap: {$sitemapUrl}
Sitemap: {$sitemapPostsUrl}
Sitemap: {$sitemapPagesUrl}
Sitemap: {$sitemapServicesUrl}
Sitemap: {$sitemapCategoriesUrl}
ROBOTS;

        return response(trim($content)."\n", 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }

    /**
     * Compile page entries for sitemaps.
     */
    protected function getPagesEntries(): array
    {
        $pages = Page::where('status', 'published')
            ->where('is_indexable', true)
            ->whereNotIn('slug', self::EXCLUDED_PAGE_SLUGS)
            ->latest('updated_at')
            ->get();

        $entries = [];

        // Core Landing Pages
        $entries[] = [
            'loc' => $this->formatSitemapUrl('/'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
            'image' => [
                'loc' => 'https://lifeguarddatarecovery.com/wp-content/uploads/2022/03/aboutus.jpg',
                'title' => 'LifeGuard Data Recovery Dubai - #1 Trusted Data Recovery Lab',
            ],
        ];

        $entries[] = [
            'loc' => $this->formatSitemapUrl('/services'),
            'lastmod' => Service::published()->latest('updated_at')->first()?->updated_at?->toAtomString() ?? now()->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.95',
        ];

        $entries[] = [
            'loc' => $this->formatSitemapUrl('/blog'),
            'lastmod' => Post::published()->latest('updated_at')->first()?->updated_at?->toAtomString() ?? now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '0.9',
        ];

        // Standalone CMS Pages (preserving exact WP URL /slug)
        foreach ($pages as $page) {
            $entry = [
                'loc' => $this->formatSitemapUrl('/'.$page->slug),
                'lastmod' => $page->updated_at?->toAtomString() ?? now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => in_array($page->slug, ['about-us', 'contact', 'data-recovery-dubai', 'hard-drive-data-recovery-dubai', 'ssd-drive-data-recovery-in-dubai', 'nas-data-recovery-dubai', 'raid-data-recovery-dubai']) ? '0.95' : '0.8',
            ];

            if ($page->featured_image_url) {
                $entry['image'] = [
                    'loc' => $page->featured_image_url,
                    'title' => $page->title,
                ];
            }

            $entries[] = $entry;
        }

        return $entries;
    }

    /**
     * Compile commercial service entries for sitemaps.
     */
    protected function getServicesEntries(): array
    {
        $services = Service::published()
            ->where('is_indexable', true)
            ->latest('updated_at')
            ->get();

        $entries = [];
        foreach ($services as $service) {
            $entry = [
                'loc' => $this->formatSitemapUrl('/'.$service->slug),
                'lastmod' => $service->updated_at?->toAtomString() ?? now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ];

            if ($service->featured_image_url) {
                $entry['image'] = [
                    'loc' => $service->featured_image_url,
                    'title' => $service->title.' - LifeGuard Data Recovery',
                ];
            }

            $entries[] = $entry;
        }

        return $entries;
    }

    /**
     * Compile published blog posts entries for sitemaps.
     */
    protected function getPostsEntries(): array
    {
        $posts = Post::published()
            ->where('is_indexable', true)
            ->latest('updated_at')
            ->get();

        $entries = [];
        foreach ($posts as $post) {
            // Live WordPress format is /{slug}/ - directly at the root
            $entry = [
                'loc' => $this->formatSitemapUrl('/'.$post->slug),
                'lastmod' => ($post->updated_at ?? $post->published_at ?? now())->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.75',
            ];

            if ($post->featured_image_url) {
                $entry['image'] = [
                    'loc' => $post->featured_image_url,
                    'title' => $post->title,
                ];
            }

            $entries[] = $entry;
        }

        return $entries;
    }

    /**
     * Compile active category entries for sitemaps.
     */
    protected function getCategoriesEntries(): array
    {
        $categories = Category::has('publishedPosts')
            ->with(['publishedPosts' => fn ($q) => $q->latest('updated_at')->take(1)])
            ->get();

        $entries = [];
        foreach ($categories as $category) {
            $latestPost = $category->publishedPosts->first();
            $lastmod = $latestPost?->updated_at?->toAtomString() ?? $category->updated_at?->toAtomString() ?? now()->toAtomString();

            $entries[] = [
                'loc' => $this->formatSitemapUrl('/category/'.$category->slug),
                'lastmod' => $lastmod,
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ];
        }

        return $entries;
    }

    /**
     * Compile author entries for sitemaps.
     */
    protected function getAuthorsEntries(): array
    {
        $authors = User::whereHas('posts', fn ($q) => $q->where('status', 'published'))->get();

        $entries = [];
        foreach ($authors as $author) {
            $authorSlug = $author->slug ?: Str::slug($author->name);
            $latestPost = $author->posts()->where('status', 'published')->latest('updated_at')->first();
            $lastmod = $latestPost?->updated_at?->toAtomString() ?? $author->updated_at?->toAtomString() ?? now()->toAtomString();

            $entries[] = [
                'loc' => $this->formatSitemapUrl('/author/'.$authorSlug),
                'lastmod' => $lastmod,
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ];
        }

        return $entries;
    }

    /**
     * Build valid XML urlset string with optional Google image extensions.
     */
    protected function buildUrlsetXml(array $entries): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'."\n";

        foreach ($entries as $entry) {
            $loc = $this->formatSitemapUrl($entry['loc']);
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.htmlspecialchars($loc, ENT_XML1, 'UTF-8')."</loc>\n";
            $xml .= "    <lastmod>{$entry['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$entry['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$entry['priority']}</priority>\n";

            if (! empty($entry['image']) && ! empty($entry['image']['loc'])) {
                $imageLoc = $this->formatSitemapUrl($entry['image']['loc']);
                $xml .= "    <image:image>\n";
                $xml .= '      <image:loc>'.htmlspecialchars($imageLoc, ENT_XML1, 'UTF-8')."</image:loc>\n";
                if (! empty($entry['image']['title'])) {
                    $xml .= '      <image:title>'.htmlspecialchars($entry['image']['title'], ENT_XML1, 'UTF-8')."</image:title>\n";
                }
                $xml .= "    </image:image>\n";
            }

            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Build valid XML sitemapindex string.
     */
    protected function buildIndexXml(array $sitemaps): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($sitemaps as $sitemap) {
            $loc = $this->formatSitemapUrl($sitemap['loc']);
            $xml .= "  <sitemap>\n";
            $xml .= '    <loc>'.htmlspecialchars($loc, ENT_XML1, 'UTF-8')."</loc>\n";
            $xml .= "    <lastmod>{$sitemap['lastmod']}</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }

        $xml .= '</sitemapindex>';

        return $xml;
    }

    /**
     * Resolve the canonical base URL for public sitemaps.
     */
    protected function getBaseUrl(): string
    {
        if (app()->environment('testing')) {
            return rtrim(url('/'), '/');
        }

        $appUrl = config('app.url');
        if (! empty($appUrl) && ! str_contains($appUrl, 'localhost') && ! str_contains($appUrl, '127.0.0.1')) {
            return rtrim($appUrl, '/');
        }

        return 'https://lifeguarddatarecovery.com';
    }

    /**
     * Format a path or URL into a canonical sitemap URL.
     */
    protected function formatSitemapUrl(string $urlOrPath): string
    {
        $baseUrl = $this->getBaseUrl();

        if (preg_match('#^https?://#i', $urlOrPath)) {
            $parsed = parse_url($urlOrPath);
            $host = strtolower($parsed['host'] ?? '');

            $appHost = strtolower(parse_url(config('app.url') ?? '', PHP_URL_HOST) ?? '');
            $isLocalOrAppHost = in_array($host, ['localhost', '127.0.0.1', 'lifeguarddatarecovery.com', 'www.lifeguarddatarecovery.com', $appHost], true)
                || str_ends_with($host, '.test')
                || str_ends_with($host, '.local')
                || empty($host);

            if ($isLocalOrAppHost) {
                $path = $parsed['path'] ?? '';
                $path = preg_replace('#/+#', '/', $path);
                $cleanPath = ltrim($path, '/');
                $fullUrl = $cleanPath === '' ? rtrim($baseUrl, '/') : rtrim($baseUrl, '/').'/'.$cleanPath;

                if (! empty($parsed['query'])) {
                    $fullUrl .= '?'.$parsed['query'];
                }

                return $fullUrl;
            }

            return $urlOrPath;
        }

        $cleanPath = preg_replace('#/+#', '/', $urlOrPath);
        $cleanPath = ltrim($cleanPath, '/');

        return $cleanPath === '' ? rtrim($baseUrl, '/') : rtrim($baseUrl, '/').'/'.$cleanPath;
    }

    /**
     * Generate an environment- and domain-scoped cache key to prevent cross-environment cache leakage.
     */
    protected function getCacheKey(string $key): string
    {
        return $key.'_'.md5($this->getBaseUrl());
    }
}

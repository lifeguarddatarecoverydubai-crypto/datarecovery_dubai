<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Throwable;

class RedirectRule extends Model
{
    use HasFactory;

    public const CACHE_KEY = 'cms_active_redirect_rules_v2';

    protected $fillable = [
        'source_url',
        'target_url',
        'status_code',
        'match_type',
        'query_param_option',
        'group',
        'description',
        'is_active',
        'hits',
        'last_accessed_at',
        'order',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'is_active' => 'boolean',
        'hits' => 'integer',
        'last_accessed_at' => 'datetime',
        'order' => 'integer',
    ];

    /**
     * Boot the model to auto-clear redirect cache.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    /**
     * Clear cached active redirects.
     */
    public static function clearCache(): void
    {
        try {
            Cache::forget(self::CACHE_KEY);
            Cache::forget('cms_active_redirect_rules');
        } catch (Throwable) {
            // Ignore cache clearing errors
        }
    }

    /**
     * Get all active redirect rules as clean scalar arrays from cache.
     * Caching plain arrays prevents __PHP_Incomplete_Class serialization issues across processes.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getActiveCachedRules(): array
    {
        try {
            $cached = Cache::rememberForever(self::CACHE_KEY, function () {
                return static::active()
                    ->orderBy('order', 'asc')
                    ->orderBy('id', 'asc')
                    ->get([
                        'id',
                        'source_url',
                        'target_url',
                        'status_code',
                        'match_type',
                        'query_param_option',
                        'group',
                        'description',
                        'is_active',
                    ])
                    ->toArray();
            });

            if (! is_array($cached)) {
                static::clearCache();

                return static::fetchActiveRulesArray();
            }

            return $cached;
        } catch (Throwable) {
            // Safe fallback: direct database query on any cache failure
            return static::fetchActiveRulesArray();
        }
    }

    /**
     * Fetch active rules directly from DB as array.
     *
     * @return array<int, array<string, mixed>>
     */
    protected static function fetchActiveRulesArray(): array
    {
        try {
            return static::active()
                ->orderBy('order', 'asc')
                ->orderBy('id', 'asc')
                ->get([
                    'id',
                    'source_url',
                    'target_url',
                    'status_code',
                    'match_type',
                    'query_param_option',
                    'group',
                    'description',
                    'is_active',
                ])
                ->toArray();
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * Scope for active rules.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for search filtering.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->where('source_url', 'like', "%{$term}%")
                ->orWhere('target_url', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('group', 'like', "%{$term}%");
        });
    }

    /**
     * Scope for group filtering.
     */
    public function scopeGroup(Builder $query, ?string $group): Builder
    {
        if (empty($group) || $group === 'all') {
            return $query;
        }

        return $query->where('group', $group);
    }

    /**
     * Normalize URL for clean matching and storage.
     */
    public static function normalizeUrl(string $url, bool $isSource = true): string
    {
        $trimmed = trim($url);

        if ($trimmed === '' || $trimmed === '#') {
            return $isSource ? '/' : '#';
        }

        // If it's a regular expression or wildcard pattern for source, preserve regex tokens
        if ($isSource && (str_starts_with($trimmed, '^') || (! str_starts_with($trimmed, '/') && str_contains($trimmed, '(')))) {
            return $trimmed;
        }

        // Check if full URL provided (e.g. contains http:// or https://)
        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            $parsed = parse_url($trimmed);
            $appHost = parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST);
            $parsedHost = $parsed['host'] ?? '';

            // If it's the current host or if normalizing source URL, strip domain
            if ($isSource || ($parsedHost && strtolower($parsedHost) === strtolower($appHost))) {
                $path = $parsed['path'] ?? '/';
                $query = isset($parsed['query']) ? '?'.$parsed['query'] : '';
                $trimmed = $path.$query;
            } else {
                // External target URL, keep as is
                return $trimmed;
            }
        }

        // Target can be an external URL
        if (! $isSource && (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://'))) {
            return $trimmed;
        }

        // Ensure single leading slash
        $pathOnly = parse_url($trimmed, PHP_URL_PATH) ?? '';
        $queryOnly = parse_url($trimmed, PHP_URL_QUERY);

        // Normalize slashes
        $pathOnly = preg_replace('#/+#', '/', $pathOnly) ?: '/';
        if (! str_starts_with($pathOnly, '/')) {
            $pathOnly = '/'.$pathOnly;
        }

        // Remove trailing slash unless root '/'
        if (strlen($pathOnly) > 1 && str_ends_with($pathOnly, '/')) {
            $pathOnly = rtrim($pathOnly, '/');
        }

        return $queryOnly ? $pathOnly.'?'.$queryOnly : $pathOnly;
    }

    /**
     * Evaluate matching logic using array attributes.
     *
     * @param  array<string, mixed>  $ruleData
     */
    public static function evaluateRuleArrayMatch(array $ruleData, string $requestUri, string $requestPath, ?string $queryString): ?string
    {
        $sourceUrl = $ruleData['source_url'] ?? '';
        $targetUrl = $ruleData['target_url'] ?? '';
        $statusCode = (int) ($ruleData['status_code'] ?? 301);
        $matchType = $ruleData['match_type'] ?? 'exact';
        $queryParamOption = $ruleData['query_param_option'] ?? 'ignore';

        $normalizedPath = static::normalizeUrl($requestPath, true);
        $normalizedSource = static::normalizeUrl($sourceUrl, true);

        $matched = false;
        $resolvedTarget = $targetUrl;

        if ($matchType === 'exact') {
            if ($queryParamOption === 'exact') {
                $reqFull = $queryString ? $normalizedPath.'?'.$queryString : $normalizedPath;
                $matched = (strtolower($reqFull) === strtolower($normalizedSource));
            } else {
                $matched = (strtolower($normalizedPath) === strtolower($normalizedSource));
            }
        } elseif ($matchType === 'wildcard') {
            $pattern = preg_quote($normalizedSource, '#');
            $pattern = str_replace('\\*', '(.*)', $pattern);
            if (preg_match('#^'.$pattern.'$#i', $normalizedPath, $matches)) {
                $matched = true;
                $replacement = $matches[1] ?? '';
                if (str_contains($resolvedTarget, '$1')) {
                    $resolvedTarget = str_replace('$1', $replacement, $resolvedTarget);
                } elseif (str_ends_with($resolvedTarget, '*')) {
                    $resolvedTarget = rtrim($resolvedTarget, '*').$replacement;
                } elseif (str_ends_with($resolvedTarget, '/')) {
                    $resolvedTarget = $resolvedTarget.$replacement;
                }
            }
        } elseif ($matchType === 'regex') {
            $regex = trim($sourceUrl);
            if (! str_starts_with($regex, '#')) {
                $regex = '#'.$regex.'#i';
            }
            if (@preg_match($regex, $normalizedPath, $matches)) {
                $matched = true;
                $resolvedTarget = preg_replace($regex, $targetUrl, $normalizedPath);
            }
        }

        if (! $matched) {
            return null;
        }

        if ($statusCode === 410) {
            return '410_GONE';
        }

        if ($queryParamOption === 'pass' && ! empty($queryString)) {
            $separator = str_contains($resolvedTarget, '?') ? '&' : '?';
            $resolvedTarget .= $separator.$queryString;
        }

        // Loop prevention
        $targetPath = parse_url($resolvedTarget, PHP_URL_PATH) ?? '';
        $targetQuery = parse_url($resolvedTarget, PHP_URL_QUERY);
        if ($targetPath === $normalizedPath && $targetQuery === $queryString) {
            return null;
        }

        return $resolvedTarget;
    }

    /**
     * Evaluate if this model instance matches the incoming request.
     */
    public function evaluateMatch(string $requestUri, string $requestPath, ?string $queryString): ?string
    {
        return static::evaluateRuleArrayMatch($this->attributesToArray(), $requestUri, $requestPath, $queryString);
    }

    /**
     * Match incoming HTTP request against active rules.
     *
     * @return array{rule_id: int, target_url: string, status_code: int, rule_data: array<string, mixed>}|null
     */
    public static function matchRequest(Request $request): ?array
    {
        $requestPath = '/'.ltrim($request->path(), '/');
        $queryString = $request->getQueryString();
        $requestUri = $queryString ? $requestPath.'?'.$queryString : $requestPath;

        $rules = static::getActiveCachedRules();

        foreach ($rules as $ruleData) {
            $target = static::evaluateRuleArrayMatch($ruleData, $requestUri, $requestPath, $queryString);

            if ($target !== null) {
                return [
                    'rule_id' => (int) $ruleData['id'],
                    'target_url' => $target,
                    'status_code' => (int) $ruleData['status_code'],
                    'rule_data' => $ruleData,
                ];
            }
        }

        return null;
    }

    /**
     * Record a hit on a redirect rule by ID without loading the model.
     */
    public static function recordHitById(int $id): void
    {
        try {
            static::where('id', $id)->increment('hits', 1, [
                'last_accessed_at' => now(),
            ]);
        } catch (Throwable) {
            // Ignore tracking errors to never interrupt visitor redirect
        }
    }

    /**
     * Record a hit on this redirect rule instance.
     */
    public function recordHit(): void
    {
        static::recordHitById($this->id);
    }

    /**
     * Pre-populate default legacy redirects from hardcoded permalinks.
     */
    public static function seedDefaults(): void
    {
        $defaults = [
            [
                'source_url' => '/hard-drive-data-recovery-in-dubai',
                'target_url' => '/hard-drive-recovery',
                'status_code' => 301,
                'match_type' => 'exact',
                'query_param_option' => 'pass',
                'group' => 'SEO Cannibalization',
                'description' => 'Legacy cannibalizing hard drive recovery permalink consolidated into official lab service.',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'source_url' => '/server-recovery-dubai',
                'target_url' => '/raid-recovery-dubai',
                'status_code' => 301,
                'match_type' => 'exact',
                'query_param_option' => 'pass',
                'group' => 'SEO Cannibalization',
                'description' => 'Consolidates server recovery queries into high-authority RAID & Enterprise storage service.',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'source_url' => '/sd-card-recovery-dubai',
                'target_url' => '/usb-data-recovery-dubai',
                'status_code' => 301,
                'match_type' => 'exact',
                'query_param_option' => 'pass',
                'group' => 'SEO Cannibalization',
                'description' => 'Directs legacy SD card recovery visitors to flash memory & NAND recovery page.',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'source_url' => '/home',
                'target_url' => '/',
                'status_code' => 301,
                'match_type' => 'exact',
                'query_param_option' => 'ignore',
                'group' => 'WordPress Legacy',
                'description' => 'WordPress default /home permalink redirected to clean root domain.',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'source_url' => '/post/ssd-recovery-dubai',
                'target_url' => '/ssd-recovery-dubai',
                'status_code' => 301,
                'match_type' => 'exact',
                'query_param_option' => 'pass',
                'group' => 'SEO Cannibalization',
                'description' => 'Redirects conflicting blog post slug directly to commercial SSD recovery service.',
                'is_active' => true,
                'order' => 5,
            ],
        ];

        foreach ($defaults as $item) {
            static::firstOrCreate(
                ['source_url' => $item['source_url']],
                $item
            );
        }
    }
}

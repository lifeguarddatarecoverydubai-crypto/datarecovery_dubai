<?php

namespace App\Models;

use App\Models\Concerns\HasFeaturedImage;
use App\Models\Concerns\HasSanitizedContent;
use App\Services\YoastSeoService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, HasFeaturedImage, HasSanitizedContent;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'is_indexable',
        'focus_keyword',
        'seo_score',
        'readability_score',
        'schema_markup',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_indexable' => 'boolean',
            'seo_score' => 'integer',
            'readability_score' => 'integer',
        ];
    }

    /**
     * Model booted hooks.
     */
    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            if ($post->seo_score === null || $post->seo_score <= 0) {
                $scores = YoastSeoService::calculateForModel($post);
                $post->seo_score = $scores['seo_score'];
                if ($post->readability_score === null || $post->readability_score <= 0) {
                    $post->readability_score = $scores['readability_score'];
                }
            }
        });
    }

    /**
     * Canonical public permalink URL matching live WordPress site.
     */
    public function getUrlAttribute(): string
    {
        return url('/'.$this->slug);
    }

    /**
     * Author relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'LifeGuard Data Recovery Team',
            'email' => 'info@lifeguarddatarecovery.com',
        ]);
    }

    /**
     * Post categories.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Post tags.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Scope for published posts.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope for searching posts.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%")
                ->orWhere('content', 'like', "%{$term}%");
        });
    }

    /**
     * Return a complete excerpt for display, removing imported truncation markers.
     */
    public function getExcerptAttribute(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        if (($this->attributes['slug'] ?? null) === 'what-causes-ssd-slowdowns-and-how-to-fix-them') {
            return 'A slow SSD can be caused by low free space, background activity, heat, outdated firmware, or a developing drive fault. Learn how to check performance safely, protect your files, and recognize when troubleshooting should stop and data recovery help may be needed.';
        }

        return self::normalizeExcerpt($value);
    }

    public static function normalizeExcerpt(?string $excerpt): ?string
    {
        if ($excerpt === null) {
            return null;
        }

        $excerpt = trim($excerpt);
        $hasTruncationMarker = preg_match('/\s*(?:\[(?:…|\.\.\.)\]|…|\.\.\.)\s*$/u', $excerpt) === 1;

        if (! $hasTruncationMarker) {
            return $excerpt;
        }

        $excerpt = preg_replace('/\s*(?:\[(?:…|\.\.\.)\]|…|\.\.\.)\s*$/u', '', $excerpt) ?? $excerpt;
        preg_match_all('/[.!?](?=\s|$)/u', $excerpt, $matches, PREG_OFFSET_CAPTURE);

        if (empty($matches[0])) {
            return '';
        }

        $lastSentenceEnd = end($matches[0])[1];

        return trim(substr($excerpt, 0, $lastSentenceEnd + 1));
    }

    /**
     * Estimated reading time in minutes.
     */
    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags($this->content ?? ''));

        return max(1, (int) ceil($words / 200));
    }

    /**
     * SEO Title fallback.
     */
    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title ?: $this->title;
    }

    /**
     * SEO Description fallback.
     */
    public function getSeoDescriptionAttribute(): string
    {
        if ($this->meta_description) {
            return $this->meta_description;
        }

        if ($this->excerpt) {
            return Str::limit(strip_tags($this->excerpt), 160);
        }

        return Str::limit(strip_tags($this->content ?? ''), 160);
    }

    /**
     * SEO Image fallback.
     */
    public function getSeoImageAttribute(): ?string
    {
        return $this->og_image ?: $this->featured_image_url;
    }

    /**
     * Automatically generate unique slug.
     */
    public static function createUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}

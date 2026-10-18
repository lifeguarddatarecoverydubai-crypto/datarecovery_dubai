<?php

namespace App\Models;

use App\Models\Concerns\HasFeaturedImage;
use App\Services\YoastSeoService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory, HasFeaturedImage;

    protected $fillable = [
        'title',
        'slug',
        'tagline',
        'hero_badge',
        'icon',
        'summary',
        'content',
        'features',
        'benefits',
        'benchmarks',
        'featured_image',
        'status',
        'order',
        'is_featured',
        'focus_keyword',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'is_indexable',
        'seo_score',
        'readability_score',
        'schema_markup',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'benefits' => 'array',
            'benchmarks' => 'array',
            'is_featured' => 'boolean',
            'is_indexable' => 'boolean',
            'order' => 'integer',
            'seo_score' => 'integer',
            'readability_score' => 'integer',
        ];
    }

    /**
     * Model booted hooks.
     */
    protected static function booted(): void
    {
        static::saving(function (Service $service) {
            if ($service->seo_score === null || $service->seo_score <= 0) {
                $scores = YoastSeoService::calculateForModel($service);
                $service->seo_score = $scores['seo_score'];
                if ($service->readability_score === null || $service->readability_score <= 0) {
                    $service->readability_score = $scores['readability_score'];
                }
            }
        });
    }

    /**
     * Get effective hero badge with standard default.
     */
    public function getEffectiveHeroBadgeAttribute(): string
    {
        return $this->hero_badge ?: 'UAE Cleanroom Lab • ISO-5 Class 100 & PC-3000 Certified Data Recovery';
    }

    /**
     * Get effective benchmarks array with lab defaults.
     */
    public function getEffectiveBenchmarksAttribute(): array
    {
        $defaults = [
            'standard' => 'ISO-5 Class 100 Cleanroom & PC-3000 Hardware Tools',
            'evaluation' => 'Free In-Lab Diagnostics & Assessment',
            'guarantee' => 'No Data, No Recovery Fee Guarantee',
            'turnaround' => '24/7 Emergency Mobilization Across Dubai & UAE',
            'support' => 'Dedicated Recovery Engineer & Direct Lab Updates',
        ];

        return array_merge($defaults, is_array($this->benchmarks) ? $this->benchmarks : []);
    }

    /**
     * Scope for published services.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for featured services.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope ordered by custom order index.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * SEO Title fallback.
     */
    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title ?: ($this->title.' - LifeGuard Data Recovery');
    }

    /**
     * SEO Description fallback.
     */
    public function getSeoDescriptionAttribute(): string
    {
        if ($this->meta_description) {
            return $this->meta_description;
        }

        if ($this->summary) {
            return Str::limit(strip_tags($this->summary), 160);
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

    /**
     * Extract structured FAQs from service content for Schema.org JSON-LD.
     *
     * @return array<int, array{question: string, answer: string}>
     */
    public function getFaqsAttribute(): array
    {
        if (empty($this->content)) {
            return [];
        }

        $faqs = [];

        // 1. Try matching <details><summary>Question</summary><div>Answer</div></details>
        if (preg_match_all('/<details[^>]*>\s*<summary[^>]*>(.*?)<\/summary>\s*<div[^>]*>(.*?)<\/div>\s*<\/details>/is', $this->content, $detailMatches)) {
            foreach ($detailMatches[1] as $index => $rawQuestion) {
                // Remove arrows like ▼ or icons
                $cleanQ = preg_replace('/[▼▲►▼]/u', '', strip_tags($rawQuestion));
                $cleanQ = trim($cleanQ);
                $cleanA = trim(strip_tags($detailMatches[2][$index] ?? ''));
                if ($cleanQ !== '' && $cleanA !== '') {
                    $faqs[] = [
                        'question' => $cleanQ,
                        'answer' => $cleanA,
                    ];
                }
            }
        }

        // 2. Fallback or additional: <h3/h4>Question?</h3/h4><p>Answer</p>
        if (empty($faqs)) {
            preg_match_all('/<h[34][^>]*>([^<]*\?[^<]*)<\/h[34]>\s*<p[^>]*>(.*?)<\/p>/is', $this->content, $headingMatches);
            if (! empty($headingMatches[1])) {
                foreach ($headingMatches[1] as $index => $question) {
                    $q = trim(strip_tags($question));
                    $a = trim(strip_tags($headingMatches[2][$index] ?? ''));
                    if ($q !== '' && $a !== '') {
                        $faqs[] = [
                            'question' => $q,
                            'answer' => $a,
                        ];
                    }
                }
            }
        }

        return $faqs;
    }
}

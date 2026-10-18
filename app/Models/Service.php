<?php

namespace App\Models;

use App\Models\Concerns\HasFeaturedImage;
use App\Models\Concerns\HasSanitizedContent;
use App\Services\YoastSeoService;
use App\Support\ContentHtmlSanitizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory, HasFeaturedImage, HasSanitizedContent;

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
     * Return safe service content with legacy editor artifacts removed.
     */
    public function getContentAttribute(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        $content = $this->removeTrailingSeedArray($value);
        $content = app(ContentHtmlSanitizer::class)->sanitize($content);

        if ($content === null || trim($content) === '') {
            return $content;
        }

        return $this->removeRepeatedServiceContentArtifacts($content);
    }

    private function removeTrailingSeedArray(string $content): string
    {
        if (! preg_match('/[\'"]slug[\'"]\s*=>\s*[\'"][a-z0-9-]+[\'"]\s*,\s*[\'"]title[\'"]\s*=>/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
            return $content;
        }

        $artifactOffset = $matches[0][1];
        $contentBeforeArtifact = substr($content, 0, $artifactOffset);

        if (! preg_match('/\/\/[\s-]{5,}/', $contentBeforeArtifact)) {
            return $content;
        }

        if (! preg_match_all('/<\/(?:div|section|article|p|ul|ol)>/i', $contentBeforeArtifact, $closingTags, PREG_OFFSET_CAPTURE)) {
            preg_match('/\/\/[\s-]{5,}/', $contentBeforeArtifact, $commentMarker, PREG_OFFSET_CAPTURE);

            return isset($commentMarker[0][1]) ? substr($content, 0, $commentMarker[0][1]) : $content;
        }

        $lastClosingTag = $closingTags[0][count($closingTags[0]) - 1];
        $validContentEnd = $lastClosingTag[1] + strlen($lastClosingTag[0]);

        return substr($content, 0, $validContentEnd);
    }

    private function removeRepeatedServiceContentArtifacts(string $content): string
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $previousErrors = libxml_use_internal_errors(true);

        try {
            $document->loadHTML(
                '<?xml encoding="UTF-8"><!DOCTYPE html><html><body><div id="service-content-root">'.$content.'</div></body></html>',
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING
            );

            $root = $document->getElementById('service-content-root');
            if (! $root) {
                return $content;
            }

            $xpath = new \DOMXPath($document);
            $logoImages = $xpath->query('//*[@id="service-content-root"]//img');
            if ($logoImages !== false) {
                foreach (iterator_to_array($logoImages) as $image) {
                    $sourcePath = parse_url($image->getAttribute('src'), PHP_URL_PATH) ?: $image->getAttribute('src');
                    $fileName = basename($sourcePath);
                    $altText = $image->getAttribute('alt');

                    if (stripos($fileName, 'logo') !== false || stripos($altText, 'logo') !== false) {
                        $parent = $image->parentNode;
                        $parent?->removeChild($image);
                        $this->removeEmptyServiceContentContainer($parent);
                    }
                }
            }

            $bookNowLinks = $xpath->query(
                '//*[@id="service-content-root"]//a[translate(normalize-space(string(.)), "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz") = "book now"]'
            );
            if ($bookNowLinks !== false) {
                foreach (iterator_to_array($bookNowLinks) as $link) {
                    $parent = $link->parentNode;
                    $parent?->removeChild($link);
                    $this->removeEmptyServiceContentContainer($parent);
                }
            }

            $result = '';
            foreach ($root->childNodes as $child) {
                $result .= $document->saveHTML($child);
            }

            return $result;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrors);
        }
    }

    private function removeEmptyServiceContentContainer(?\DOMNode $node): void
    {
        while ($node instanceof \DOMElement && in_array(strtolower($node->tagName), ['a', 'b', 'em', 'figure', 'p', 'span', 'strong'], true)) {
            if (
                trim($node->textContent) !== ''
                || $node->getElementsByTagName('img')->length > 0
                || $node->getElementsByTagName('a')->length > 0
            ) {
                return;
            }

            $parent = $node->parentNode;
            $parent?->removeChild($node);
            $node = $parent;
        }
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

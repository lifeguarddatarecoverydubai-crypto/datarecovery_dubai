<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class YoastSeoService
{
    /**
     * Calculate SEO Score (0 - 100) and Readability Score (0 - 100)
     * matching the exact Yoast algorithm used in the admin interface.
     */
    public static function calculateScores(
        ?string $focusKeyword,
        ?string $title,
        ?string $metaTitle,
        ?string $metaDescription,
        ?string $slug,
        ?string $contentHtml
    ): array {
        $keyword = mb_strtolower(trim((string) $focusKeyword));
        $effectiveTitle = trim(! empty($metaTitle) ? $metaTitle : (string) $title);
        $fullDesc = trim((string) $metaDescription);
        $slug = trim((string) $slug);

        // Extract raw text from HTML content
        $plainContent = trim(preg_replace('/\s+/', ' ', strip_tags((string) $contentHtml)));
        $words = ! empty($plainContent) ? preg_split('/\s+/', $plainContent, -1, PREG_SPLIT_NO_EMPTY) : [];
        $wordCount = count($words);

        $earnedSeoPoints = 0;
        $totalSeoPoints = 0;

        // 1. Focus Keyword defined (10 pts)
        $totalSeoPoints += 10;
        if (! empty($keyword)) {
            $earnedSeoPoints += 10;
        }

        // 2. Keyphrase in SEO Title (15 pts)
        $totalSeoPoints += 15;
        $lowerTitle = mb_strtolower($effectiveTitle);
        if (! empty($keyword) && (str_contains($lowerTitle, $keyword) || self::containsKeywordWords($lowerTitle, $keyword))) {
            $earnedSeoPoints += 15;
        }

        // 3. SEO Title Length (15 pts)
        $totalSeoPoints += 15;
        $titleLen = mb_strlen($effectiveTitle);
        if ($titleLen >= 35 && $titleLen <= 65) {
            $earnedSeoPoints += 15;
        } elseif ($titleLen > 65 || $titleLen >= 20) {
            $earnedSeoPoints += 10;
        } elseif ($titleLen > 0) {
            $earnedSeoPoints += 5;
        }

        // 4. Keyphrase in Meta Description (15 pts)
        $totalSeoPoints += 15;
        $lowerDesc = mb_strtolower($fullDesc);
        if (! empty($keyword) && (str_contains($lowerDesc, $keyword) || self::containsKeywordWords($lowerDesc, $keyword))) {
            $earnedSeoPoints += 15;
        }

        // 5. Meta Description Length (15 pts)
        $totalSeoPoints += 15;
        $descLen = mb_strlen($fullDesc);
        if ($descLen >= 115 && $descLen <= 165) {
            $earnedSeoPoints += 15;
        } elseif ($descLen > 165 || $descLen >= 70) {
            $earnedSeoPoints += 10;
        } elseif ($descLen > 0) {
            $earnedSeoPoints += 5;
        }

        // 6. Keyphrase in URL Slug (15 pts)
        $totalSeoPoints += 15;
        $normalizedSlug = str_replace(' ', '-', $keyword);
        $cleanSlug = str_replace(['_', '/'], '-', mb_strtolower($slug));
        if (! empty($keyword) && (str_contains($cleanSlug, $normalizedSlug) || self::containsKeywordWords($cleanSlug, $keyword, '-'))) {
            $earnedSeoPoints += 15;
        } elseif (! empty($keyword)) {
            $earnedSeoPoints += 8;
        }

        // 7. Word Count (15 pts)
        $totalSeoPoints += 15;
        if ($wordCount >= 250) {
            $earnedSeoPoints += 15;
        } elseif ($wordCount >= 100) {
            $earnedSeoPoints += 10;
        } elseif ($wordCount > 0) {
            $earnedSeoPoints += 5;
        }

        $seoScore = $totalSeoPoints > 0 ? (int) min(100, round(($earnedSeoPoints / $totalSeoPoints) * 100)) : 0;

        // Readability checks (100 pts)
        $earnedReadPoints = 0;
        $totalReadPoints = 100;

        $hasHeadings = (bool) preg_match('/<h[1-4][^>]*>/i', (string) $contentHtml);
        if ($hasHeadings) {
            $earnedReadPoints += 50;
        } else {
            $earnedReadPoints += 25;
        }

        if ($wordCount >= 150) {
            $earnedReadPoints += 50;
        } elseif ($wordCount >= 50) {
            $earnedReadPoints += 35;
        } else {
            $earnedReadPoints += 20;
        }

        $readabilityScore = (int) min(100, round(($earnedReadPoints / $totalReadPoints) * 100));

        return [
            'seo_score' => $seoScore,
            'readability_score' => $readabilityScore,
        ];
    }

    /**
     * Calculate scores directly for any Post, Page, or Service model.
     */
    public static function calculateForModel(Model $model): array
    {
        return self::calculateScores(
            $model->focus_keyword ?? null,
            $model->title ?? null,
            $model->meta_title ?? null,
            $model->meta_description ?? null,
            $model->slug ?? null,
            $model->content ?? null
        );
    }

    /**
     * Check if text contains the individual core terms of the keyword.
     */
    protected static function containsKeywordWords(string $text, string $keyword, string $separator = ' '): bool
    {
        $words = array_filter(explode(' ', $keyword), fn ($w) => mb_strlen($w) > 2);
        if (empty($words)) {
            return false;
        }

        $matched = 0;
        foreach ($words as $w) {
            if (str_contains($text, $w)) {
                $matched++;
            }
        }

        return ($matched / count($words)) >= 0.75;
    }
}

<?php

namespace App\Support;

class SeoTitleNormalizer
{
    /**
     * Strip a trailing " - Site Name" / " | Site Name" suffix that Yoast already
     * baked into the WP title, so the frontend doesn't append the brand twice.
     * Also title-cases the remaining text intelligently.
     */
    public static function stripSiteSuffix(string $title): string
    {
        return SeoHelper::formatTitle($title, false);
    }

    /**
     * Title-case a WP title while preserving known storage/tech acronyms.
     */
    public static function normalizeTitleCase(string $title): string
    {
        return SeoHelper::formatTitle($title, false);
    }
}

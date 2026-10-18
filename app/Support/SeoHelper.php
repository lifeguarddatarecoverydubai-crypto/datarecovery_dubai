<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class SeoHelper
{
    /**
     * Tech acronyms and brand names that must preserve exact casing.
     */
    protected const PRESERVED_ACRONYMS = [
        'SSD', 'HDD', 'RAID', 'NAS', 'SAN', 'NVMe', 'SATA', 'SAS', 'SCSI',
        'PCIe', 'PCI-E', 'BIOS', 'UEFI', 'USB', 'SD', 'CCTV', 'DVR', 'NVR',
        'OS', 'Mac', 'macOS', 'Apple', 'iOS', 'iPhone', 'iPad', 'Android',
        'PC', 'IT', 'UAE', 'Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman',
        'NAND', 'NOR', 'RAM', 'DRAM', 'SRAM', 'VRAM', 'CPU', 'GPU',
        'SMART', 'S.M.A.R.T.', 'SQL', 'MySQL', 'MSSQL', 'NTFS', 'FAT32',
        'exFAT', 'APFS', 'HFS+', 'EXT4', 'Btrfs', 'ZFS', 'VMware', 'ESXi',
        'Hyper-V', 'Proxmox', 'Qnap', 'Synology', 'Seagate', 'Western Digital',
        'WD', 'Toshiba', 'Hitachi', 'HGST', 'Samsung', 'SanDisk', 'Kingston',
        'Crucial', 'Intel', 'Micron', 'SK Hynix', 'Transcend', 'Corsair',
        'PCB', 'TVS', 'IC', 'ROM', 'EEPROM', 'Bad Sectors', 'Zero-Fill',
        'LifeGuard', 'Cleanroom', 'ISO-5', 'Class 100', 'PC-3000',
        'SEO', 'URL', 'HTML', 'XML', 'API', 'FAQ', 'IP', 'LAN', 'WAN', 'DNS', 'SSL', 'TLS', 'FTP', 'HTTP', 'HTTPS', 'XFS',
    ];

    /**
     * Minor words that stay lowercase in Title Case unless at the start or after a delimiter.
     */
    protected const MINOR_WORDS = [
        'a', 'an', 'the', 'and', 'but', 'or', 'nor', 'for', 'yet', 'so',
        'as', 'at', 'by', 'for', 'from', 'in', 'into', 'near', 'of', 'off',
        'on', 'onto', 'per', 'to', 'up', 'via', 'with', 'vs', 'vs.',
    ];

    /**
     * Brand suffixes to strip when sanitizing titles.
     */
    protected const BRAND_SUFFIX_PATTERN = '/\s*[-|–—:]\s*(LifeGuard(\s*Data\s*Recovery)?(\s*Dubai)?|Data\s*Recovery\s*Dubai)\s*$/i';

    /**
     * Format a title into clean, professional title case while preserving tech acronyms and brand standards.
     * Optionally appends the official brand suffix once.
     */
    public static function formatTitle(?string $title, bool $appendBrand = false): string
    {
        if (empty($title)) {
            return $appendBrand ? config('app.name', 'LifeGuard Data Recovery') : '';
        }

        $cleaned = trim($title);

        // Strip any existing brand suffixes repeatedly until clean
        while (preg_match(self::BRAND_SUFFIX_PATTERN, $cleaned)) {
            $cleaned = preg_replace(self::BRAND_SUFFIX_PATTERN, '', $cleaned);
            $cleaned = trim($cleaned);
        }

        // Clean any double spaces
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);

        // Split words and apply intelligent title casing
        $words = explode(' ', $cleaned);
        $totalWords = count($words);
        $formattedWords = [];

        // Build a lookup map for fast case-insensitive acronym matching
        $acronymMap = [];
        foreach (self::PRESERVED_ACRONYMS as $acronym) {
            $acronymMap[strtolower($acronym)] = $acronym;
        }

        foreach ($words as $idx => $word) {
            // Keep hyphenated subwords
            if (str_contains($word, '-') && ! str_starts_with($word, '-') && ! str_ends_with($word, '-')) {
                $subparts = explode('-', $word);
                $formattedSubparts = array_map(function ($part) use ($acronymMap) {
                    $lower = strtolower($part);

                    return $acronymMap[$lower] ?? ucfirst($lower);
                }, $subparts);
                $formattedWords[] = implode('-', $formattedSubparts);

                continue;
            }

            // Remove trailing punctuation for matching
            preg_match('/^(.*?)([\?\!\.\,\;\:]*)$/', $word, $matches);
            $core = $matches[1] ?? $word;
            $punct = $matches[2] ?? '';
            $coreLower = strtolower($core);

            if (isset($acronymMap[$coreLower])) {
                $formattedWord = $acronymMap[$coreLower];
            } elseif (ctype_upper($core) && strlen($core) <= 4 && ! in_array($coreLower, self::MINOR_WORDS, true)) {
                $formattedWord = $core;
            } elseif ($idx > 0 && $idx < $totalWords - 1 && in_array($coreLower, self::MINOR_WORDS, true)) {
                $formattedWord = $coreLower;
            } else {
                $formattedWord = ucfirst($coreLower);
            }

            $formattedWords[] = $formattedWord.$punct;
        }

        $result = implode(' ', $formattedWords);

        if ($appendBrand) {
            $brandName = config('app.name', 'LifeGuard Data Recovery');
            $result = $result.' - '.$brandName;
        }

        return $result;
    }

    /**
     * Resolve a clean, deduplicated public URL for a featured image.
     */
    public static function featuredImageUrl(?string $rawPath): ?string
    {
        if (empty($rawPath)) {
            return null;
        }

        $rawPath = trim($rawPath);

        // Remote full URLs
        if (str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) {
            // Check if it's pointing to current localhost or 127.0.0.1
            $parsed = parse_url($rawPath);
            if (! empty($parsed['path']) && in_array($parsed['host'] ?? '', ['localhost', '127.0.0.1'], true)) {
                $rawPath = $parsed['path'];
            } else {
                return $rawPath;
            }
        }

        // Strip any leading slashes and redundant 'storage/' prefixes
        $clean = ltrim($rawPath, '/');
        while (str_starts_with($clean, 'storage/')) {
            $clean = substr($clean, 8);
            $clean = ltrim($clean, '/');
        }

        if (empty($clean)) {
            return null;
        }

        return '/storage/'.$clean;
    }

    /**
     * Resolve the canonical URL for any Model or slug.
     */
    public static function canonicalFor(Model|string $item, string $type = 'post'): string
    {
        $baseUrl = self::getBaseUrl();

        if (is_string($item)) {
            $slug = ltrim($item, '/');
            if ($type === 'category') {
                return rtrim($baseUrl, '/').'/category/'.$slug;
            }
            if ($type === 'tag') {
                return rtrim($baseUrl, '/').'/tag/'.$slug;
            }

            return rtrim($baseUrl, '/').'/'.$slug;
        }

        // If the model has an explicit override in canonical_url
        if (! empty($item->canonical_url)) {
            $parsed = parse_url($item->canonical_url);
            // If it's an external domain (syndication), honor it verbatim
            if (! empty($parsed['host']) && ! in_array(strtolower($parsed['host']), ['lifeguarddatarecovery.com', 'www.lifeguarddatarecovery.com', 'localhost', '127.0.0.1'], true)) {
                return $item->canonical_url;
            }

            $path = ltrim($parsed['path'] ?? '', '/');
            if (! empty($path)) {
                return rtrim($baseUrl, '/').'/'.$path;
            }
        }

        // Standard root permalink for Posts, Services, and Pages
        $slug = $item->slug ?? '';

        return rtrim($baseUrl, '/').'/'.ltrim($slug, '/');
    }

    /**
     * Resolve the base URL for canonicals.
     */
    protected static function getBaseUrl(): string
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
}

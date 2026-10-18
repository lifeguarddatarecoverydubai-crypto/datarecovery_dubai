<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Str;

function cleanText(string $text): string
{
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = strip_tags($text);
    $text = preg_replace('/\s+/', ' ', $text);

    return trim($text);
}

function extractSmartFocusKeyword(string $title, string $slug): string
{
    $slugClean = str_replace('-', ' ', $slug);
    $stopWords = [
        'a', 'an', 'the', 'in', 'on', 'at', 'for', 'to', 'of', 'and', 'or', 'is', 'are', 'was', 'were',
        'how', 'what', 'why', 'can', 'does', 'do', 'with', 'from', 'your', 'you', 'our', 'my', 'their',
        'them', 'best', 'guide', 'tips', 'top', 'ways', 'need', 'know', 'after', 'before', 'where', 'which',
    ];

    // Check for high-intent exact phrases first
    $lower = strtolower($title);
    $patterns = [
        'write protected ssd' => 'fix write protected ssd',
        'corrupted firmware' => 'ssd corrupted firmware recovery',
        'power surge' => 'data recovery after power surge',
        'slow hard drive' => 'slow hard drive recovery',
        'clicking hard drive' => 'clicking hard drive recovery',
        'formatted ssd' => 'formatted ssd data recovery',
        'formatted nvme' => 'formatted nvme data recovery',
        'deleted partition' => 'recover deleted partition',
        'nand flash' => 'nand flash data recovery',
        'liquid damage' => 'liquid damage data recovery',
        'water damage' => 'water damage phone recovery',
        'ransomware' => 'ransomware data recovery',
        'raid rebuild' => 'failed raid rebuild recovery',
        'nas unmounted' => 'nas volume recovery',
        'synology' => 'synology nas data recovery',
        'cctv footage' => 'cctv footage recovery',
        'iphone' => 'iphone data recovery',
        'macbook' => 'macbook data recovery',
    ];

    foreach ($patterns as $pattern => $fk) {
        if (str_contains($lower, $pattern)) {
            return $fk;
        }
    }

    $words = explode(' ', $slugClean);
    $meaningful = array_values(array_filter($words, fn ($w) => ! in_array($w, $stopWords) && strlen($w) > 2));

    if (count($meaningful) >= 4) {
        return implode(' ', array_slice($meaningful, 0, 4));
    } elseif (count($meaningful) >= 2) {
        return implode(' ', $meaningful);
    }

    return strtolower(trim(preg_replace('/[^\w\s]/', '', Str::limit($title, 35, ''))));
}

function generateKeywords(string $title, string $focus, ?string $category = null): string
{
    $kw = [];
    $kw[] = $focus;

    $lower = strtolower($title);
    if (str_contains($lower, 'ssd') || str_contains($lower, 'nvme')) {
        $kw[] = 'ssd data recovery';
        $kw[] = 'ssd data recovery dubai';
    } elseif (str_contains($lower, 'hard drive') || str_contains($lower, 'hdd') || str_contains($lower, 'external drive')) {
        $kw[] = 'hard drive data recovery';
        $kw[] = 'hard drive recovery dubai';
    } elseif (str_contains($lower, 'raid')) {
        $kw[] = 'raid data recovery';
        $kw[] = 'raid server recovery';
    } elseif (str_contains($lower, 'nas') || str_contains($lower, 'synology') || str_contains($lower, 'qnap')) {
        $kw[] = 'nas data recovery';
        $kw[] = 'nas storage recovery';
    } elseif (str_contains($lower, 'cctv') || str_contains($lower, 'dvr') || str_contains($lower, 'nvr')) {
        $kw[] = 'cctv footage recovery';
        $kw[] = 'dvr forensic recovery';
    } elseif (str_contains($lower, 'iphone') || str_contains($lower, 'mobile') || str_contains($lower, 'phone')) {
        $kw[] = 'iphone data recovery';
        $kw[] = 'mobile phone data recovery';
    } elseif (str_contains($lower, 'sd card') || str_contains($lower, 'usb') || str_contains($lower, 'flash')) {
        $kw[] = 'sd card recovery';
        $kw[] = 'usb data recovery';
    } elseif (str_contains($lower, 'mac') || str_contains($lower, 'macbook') || str_contains($lower, 'apple')) {
        $kw[] = 'macbook data recovery';
        $kw[] = 'apple data recovery';
    } elseif (str_contains($lower, 'ransomware')) {
        $kw[] = 'ransomware data recovery';
        $kw[] = 'ransomware decryption';
    } else {
        $kw[] = 'data recovery services';
        $kw[] = 'data recovery dubai';
    }

    if ($category && ! in_array(strtolower($category), ['blog', 'blogs'])) {
        $kw[] = strtolower($category);
    }

    $kw[] = 'lifeguard data recovery';
    $kw[] = 'cleanroom recovery lab';

    $unique = [];
    foreach ($kw as $k) {
        $clean = trim(strtolower($k));
        if ($clean && ! in_array($clean, $unique, true)) {
            $unique[] = $clean;
        }
    }

    return implode(', ', array_slice($unique, 0, 6));
}

echo "=== 1. FIXING SERVICES SEO (10 records) ===\n";
$servicesData = [
    'hard-drive-data-recovery-dubai' => [
        'focus' => 'hard drive data recovery dubai',
        'keywords' => 'hard drive data recovery dubai, hdd data recovery, clicking hard drive repair, external hard drive recovery, cleanroom hard drive recovery, lifeguard data recovery',
        'desc' => 'Comprehensive data recovery for clicking, failing, dropped, or corrupted hard drives in Dubai. ISO-5 Class 100 cleanroom lab. No Data, No Charge guarantee.',
    ],
    'ssd-drive-data-recovery-in-dubai' => [
        'focus' => 'ssd data recovery dubai',
        'keywords' => 'ssd data recovery dubai, nvme data recovery, solid state drive recovery, dead ssd repair, pc 3000 ssd recovery, lifeguard data recovery',
        'desc' => 'Expert recovery for solid state drives suffering from dead controller, firmware bug, or NAND flash degradation in Dubai. Free evaluation & high success rate.',
    ],
    'raid-data-recovery-service-dubai' => [
        'focus' => 'raid data recovery dubai',
        'keywords' => 'raid data recovery dubai, raid server recovery, raid 5 data recovery, raid 6 recovery, raid 10 reconstruction, enterprise raid repair',
        'desc' => 'Mission-critical enterprise data recovery for failed RAID arrays, virtualized environments, and SAN/NAS storage pools in Dubai. 24/7 priority emergency service.',
    ],
    'nas-data-recovery-dubai' => [
        'focus' => 'nas data recovery dubai',
        'keywords' => 'nas data recovery dubai, synology recovery, qnap data recovery, network attached storage recovery, btrfs ext4 raid recovery, lifeguard data recovery',
        'desc' => 'Full recovery solutions for network-attached storage devices after configuration loss, multiple drive failures, or damaged file systems. Synology & QNAP certified.',
    ],
    'server-data-recovery-dubai' => [
        'focus' => 'server data recovery dubai',
        'keywords' => 'server data recovery dubai, enterprise server recovery, vmware esxi recovery, hyper-v vhdx recovery, database server repair, datacenter data recovery',
        'desc' => 'Emergency server data recovery for enterprise database servers, Exchange mailboxes, VMware ESXi, and Hyper-V virtual environments across the UAE.',
    ],
    'cctv-footage-recovery-dubai' => [
        'focus' => 'cctv footage recovery dubai',
        'keywords' => 'cctv footage recovery dubai, dvr forensic recovery, deleted cctv recovery, overwritten surveillance video, hikvision dvr recovery, dahua recovery',
        'desc' => 'Specialized forensic recovery of overwritten, formatted, deleted, or corrupted video footage from all DVR and NVR surveillance systems in Dubai.',
    ],
    'sd-card-data-recovery' => [
        'focus' => 'sd card data recovery dubai',
        'keywords' => 'sd card data recovery, usb flash drive recovery, microsd card recovery, monolithic chip recovery, sandisk memory card recovery, lifeguard data recovery',
        'desc' => 'Precision recovery from snapped USB connectors, monolithic thumb drives, SD cards, and CFast media using advanced micro-soldering and NAND readers.',
    ],
    'iphone-data-recovery' => [
        'focus' => 'iphone data recovery dubai',
        'keywords' => 'iphone data recovery dubai, mobile phone data recovery, water damaged iphone recovery, dead phone logic board repair, android data recovery, apple data recovery',
        'desc' => 'Advanced mobile data recovery for liquid-damaged, dropped, bent, or completely unbootable iPhone and Android smartphones. Motherboard micro-soldering lab.',
    ],
    'ransomware-data-recovery' => [
        'focus' => 'ransomware data recovery dubai',
        'keywords' => 'ransomware data recovery, ransomware decryption dubai, encrypted file recovery, database ransomware recovery, lockbit recovery, cyber incident recovery',
        'desc' => 'Emergency assistance for businesses hit by ransomware attacks in Dubai. We identify malware strains, reconstruct shadow copies, and restore encrypted databases.',
    ],
    'mac-data-recovery' => [
        'focus' => 'macbook data recovery dubai',
        'keywords' => 'macbook data recovery dubai, apple mac recovery, m1 m2 m3 soldered ssd recovery, macbook logic board repair, imac data recovery, apfs data recovery',
        'desc' => 'Specialized data recovery for MacBook Pro, MacBook Air, iMac, and Mac Mini logic boards with soldered Apple Silicon SSDs and T2 security chips.',
    ],
];

foreach (Service::all() as $svc) {
    $meta = $servicesData[$svc->slug] ?? null;
    $focus = $meta['focus'] ?? strtolower($svc->title);
    $keywords = $meta['keywords'] ?? generateKeywords($svc->title, $focus, 'Services');
    $desc = $meta['desc'] ?? cleanText($svc->summary ?: $svc->meta_description);

    $svc->focus_keyword = $focus;
    $svc->meta_keywords = $keywords;
    $svc->meta_description = Str::limit($desc, 160, '');

    // Generate Rich Service Schema JSON-LD
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'serviceType' => $svc->title,
        'name' => $svc->title,
        'description' => $svc->meta_description,
        'provider' => [
            '@type' => 'ProfessionalService',
            'name' => 'LifeGuard Data Recovery Dubai',
            'url' => 'https://lifeguarddatarecovery.com',
            'telephone' => '+97143933082',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Bayat Center S25, Computer Street, Bur Dubai',
                'addressLocality' => 'Dubai',
                'addressCountry' => 'AE',
            ],
        ],
        'areaServed' => ['Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'United Arab Emirates'],
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name' => 'Data Recovery Lab Services',
            'itemListElement' => [
                [
                    '@type' => 'Offer',
                    'itemOffered' => [
                        '@type' => 'Service',
                        'name' => $svc->title,
                    ],
                    'priceCurrency' => 'AED',
                    'price' => 'Contact for Free Evaluation',
                ],
            ],
        ],
    ];
    $svc->schema_markup = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $svc->save();
    echo "Updated Service: {$svc->title}\n";
}

echo "\n=== 2. FIXING PAGES SEO (105 records) ===\n";
$missingDescFixes = [
    'data-recovery-services-in-dubai' => 'Professional Data Recovery in Dubai by LifeGuard. ISO-5 Class 100 cleanroom lab for HDD, SSD, RAID & mobile. Free diagnostic & No Data No Charge.',
    'recovery-shop-new' => 'LifeGuard Data Recovery Shop Dubai. Expert hardware repair, cleanroom data extraction, and storage media drop-off counter in Bur Dubai. Call +971 43 933 082.',
    'recovery-shop' => 'LifeGuard Data Recovery Shop Dubai. Rapid diagnostic triage and professional retrieval for failed hard drives, SSDs, and USB flash memory.',
    'lifeguard-data-recovery-landing-page-ad' => 'LifeGuard Data Recovery Dubai. Class 100 cleanroom facilities, 96.8% audited success rate, free evaluation, and guaranteed No Data No Charge policy.',
    'acronis-data-backup-solutions-dubai' => 'Enterprise Acronis Data Backup & Disaster Recovery Solutions in Dubai, UAE. Secure cloud and on-premise ransomware protection by LifeGuard.',
];

foreach (Page::all() as $page) {
    // 1. Meta Description
    if (empty($page->meta_description)) {
        if (isset($missingDescFixes[$page->slug])) {
            $page->meta_description = $missingDescFixes[$page->slug];
        } else {
            $stripped = cleanText($page->content ?? '');
            if (strlen($stripped) > 50) {
                $page->meta_description = Str::limit($stripped, 155, '');
            } else {
                $page->meta_description = "Certified {$page->title} by LifeGuard Data Recovery Dubai. ISO-5 Class 100 cleanroom lab, free evaluation, and No Data No Charge guarantee.";
            }
        }
    } else {
        $page->meta_description = cleanText($page->meta_description);
        $page->meta_description = Str::limit($page->meta_description, 160, '');
    }

    // 2. Focus Keyword
    $page->focus_keyword = extractSmartFocusKeyword($page->title, $page->slug);

    // 3. Meta Keywords
    $page->meta_keywords = generateKeywords($page->title, $page->focus_keyword, 'Page');

    // 4. Schema Markup
    if (empty($page->schema_markup) || str_contains($page->schema_markup, 'muskan begum') || str_contains($page->schema_markup, 'shafia begum') || str_contains($page->schema_markup, 'Person')) {
        $decoded = ! empty($page->schema_markup) ? json_decode($page->schema_markup, true) : null;
        if (is_array($decoded) && isset($decoded['@graph'])) {
            $filteredGraph = [];
            foreach ($decoded['@graph'] as $node) {
                if (isset($node['@type']) && $node['@type'] === 'Person') {
                    continue;
                }
                if (isset($node['name']) && (stripos($node['name'], 'muskan') !== false || stripos($node['name'], 'shafia') !== false)) {
                    continue;
                }
                $filteredGraph[] = $node;
            }
            $decoded['@graph'] = $filteredGraph;
            $schemaRes = json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            $pageSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => $page->title,
                'description' => $page->meta_description,
                'url' => url($page->slug),
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'LifeGuard Data Recovery Dubai',
                    'url' => 'https://lifeguarddatarecovery.com',
                    'logo' => 'https://lifeguarddatarecovery.com/images/logo.png',
                ],
            ];
            $schemaRes = json_encode($pageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        $schemaRes = str_ireplace(
            ['muskan begum', 'shafia begum', 'https://lifeguarddatarecovery.com/author/muskan-dubai/', 'https://lifeguarddatarecovery.com/author/shafia-dubai/'],
            ['LifeGuard Data Recovery Dubai', 'LifeGuard Data Recovery Dubai', 'https://lifeguarddatarecovery.com', 'https://lifeguarddatarecovery.com'],
            $schemaRes
        );
        $page->schema_markup = $schemaRes;
    }

    $page->save();
}
echo "Updated all 105 Pages with focus keyword, meta keywords, meta description, and valid schema markup.\n";

echo "\n=== 3. FIXING POSTS SEO (396 records) ===\n";
$postsUpdated = 0;
$schemasCleaned = 0;

foreach (Post::all() as $post) {
    // 1. Meta Description Cleanliness
    $desc = cleanText($post->meta_description ?: $post->excerpt ?: '');
    if (strlen($desc) < 40) {
        $contentClean = cleanText($post->content ?? '');
        $desc = Str::limit($contentClean, 155, '');
    } else {
        $desc = Str::limit($desc, 160, '');
    }
    $post->meta_description = $desc;

    // 2. Focus Keyword
    $post->focus_keyword = extractSmartFocusKeyword($post->title, $post->slug);

    // 3. Meta Keywords
    $cat = $post->categories()->first()?->name;
    $post->meta_keywords = generateKeywords($post->title, $post->focus_keyword, $cat);

    // 4. Schema Markup: Ensure valid schema and eliminate scraper personal authors
    if (! empty($post->schema_markup)) {
        $decoded = json_decode($post->schema_markup, true);
        if (is_array($decoded) && isset($decoded['@graph'])) {
            $filteredGraph = [];
            foreach ($decoded['@graph'] as $node) {
                if (isset($node['@type']) && $node['@type'] === 'Person') {
                    continue;
                }
                if (isset($node['name']) && (stripos($node['name'], 'muskan') !== false || stripos($node['name'], 'shafia') !== false)) {
                    continue;
                }
                if (isset($node['@type']) && in_array($node['@type'], ['Article', 'BlogPosting', 'NewsArticle'])) {
                    $node['author'] = [
                        '@type' => 'Organization',
                        'name' => 'LifeGuard Data Recovery Dubai',
                        'url' => 'https://lifeguarddatarecovery.com',
                    ];
                }
                $filteredGraph[] = $node;
            }
            $decoded['@graph'] = $filteredGraph;
            $schemaRes = json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            $schemaRes = $post->schema_markup;
        }

        $beforeScrub = $schemaRes;
        $schemaRes = str_ireplace(
            ['muskan begum', 'shafia begum', 'https://lifeguarddatarecovery.com/author/muskan-dubai/', 'https://lifeguarddatarecovery.com/author/shafia-dubai/'],
            ['LifeGuard Data Recovery Dubai', 'LifeGuard Data Recovery Dubai', 'https://lifeguarddatarecovery.com', 'https://lifeguarddatarecovery.com'],
            $schemaRes
        );
        if ($beforeScrub !== $schemaRes) {
            $schemasCleaned++;
        }
        $post->schema_markup = $schemaRes;
    } else {
        $postSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => Str::limit($post->title, 110, ''),
            'description' => $post->meta_description,
            'url' => url($post->slug),
            'datePublished' => ($post->published_at ?? $post->created_at ?? now())->toIso8601String(),
            'dateModified' => ($post->updated_at ?? now())->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => 'LifeGuard Data Recovery Dubai',
                'url' => 'https://lifeguarddatarecovery.com',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'LifeGuard Data Recovery Dubai',
                'url' => 'https://lifeguarddatarecovery.com',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => 'https://lifeguarddatarecovery.com/images/logo.png',
                ],
            ],
        ];
        $post->schema_markup = json_encode($postSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    $post->save();
    $postsUpdated++;
}

echo "Updated all {$postsUpdated} Posts with focus keyword, meta keywords, and verified meta description.\n";
echo "Cleaned personal scraper author names from {$schemasCleaned} schemas.\n";

<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Service;
use Illuminate\Support\Str;

class SchemaService
{
    /**
     * Get primary LocalBusiness / ProfessionalService schema for LifeGuard Data Recovery.
     */
    public static function getLocalBusinessSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => 'LifeGuard Data Recovery Dubai',
            'legalName' => 'LifeGuard Data Recovery LLC',
            'alternateName' => ['LifeGuard Data Recovery', 'Data Recovery Dubai', 'LifeGuard Data Lab'],
            'url' => 'https://lifeguarddatarecovery.com',
            'logo' => 'https://lifeguarddatarecovery.com/wp-content/uploads/2024/12/Desktop-2-7.png',
            'image' => 'https://lifeguarddatarecovery.com/wp-content/uploads/2022/03/aboutus.jpg',
            'telephone' => '+97143933082',
            'email' => 'info@lifeguarddatarecovery.com',
            'priceRange' => 'AED',
            'currenciesAccepted' => 'AED, USD',
            'paymentAccepted' => 'Cash, Credit Card, Bank Transfer',
            'description' => 'Dubai #1 certified Data Recovery service provider with 30 years experience. Class 100 cleanroom lab for HDD, SSD, RAID, NAS, CCTV, and mobile devices.',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Bayat Center S25, Computer Street, Bur Dubai',
                'addressLocality' => 'Dubai',
                'addressRegion' => 'Dubai',
                'postalCode' => '00000',
                'addressCountry' => 'AE',
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 25.2532,
                'longitude' => 55.2974,
            ],
            'openingHoursSpecification' => [
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                    'opens' => '09:00',
                    'closes' => '21:00',
                ],
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                    'opens' => '00:00',
                    'closes' => '23:59',
                    'description' => '24/7 Emergency Data Recovery Hotline',
                ],
            ],
            'areaServed' => [
                'Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Ras Al Khaimah', 'Fujairah', 'Umm Al Quwain', 'Al Ain', 'UAE',
            ],
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => '4.9',
                'reviewCount' => '540',
                'bestRating' => '5',
                'worstRating' => '1',
            ],
            'sameAs' => [
                'https://www.facebook.com/LifeGUardDataRecovery',
                'https://wa.me/971554838545',
            ],
        ];
    }

    /**
     * Get Article / BlogPosting schema for a Post.
     */
    public static function getArticleSchema(Post $post): array
    {
        $imageUrl = $post->featured_image_url ?: 'https://lifeguarddatarecovery.com/wp-content/uploads/2022/03/aboutus.jpg';

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => url($post->slug),
            ],
            'headline' => Str::limit($post->title, 110, ''),
            'description' => Str::limit($post->excerpt ?: $post->meta_description, 160, ''),
            'image' => [
                '@type' => 'ImageObject',
                'url' => $imageUrl,
            ],
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
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => 'https://lifeguarddatarecovery.com/wp-content/uploads/2024/12/Desktop-2-7.png',
                ],
            ],
        ];
    }

    /**
     * Get Service schema for a Service.
     */
    public static function getServiceSchema(Service $service): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service->title,
            'serviceType' => 'Data Recovery Service',
            'provider' => [
                '@type' => 'LocalBusiness',
                'name' => 'LifeGuard Data Recovery Dubai',
                'telephone' => '+97143933082',
                'url' => 'https://lifeguarddatarecovery.com',
            ],
            'areaServed' => [
                'Dubai', 'Abu Dhabi', 'Sharjah', 'United Arab Emirates',
            ],
            'description' => $service->summary ?: $service->meta_description,
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'AED',
                'description' => 'Free Diagnostic & Evaluation - No Data, No Charge Policy',
            ],
        ];
    }

    /**
     * Get BreadcrumbList schema.
     */
    public static function getBreadcrumbSchema(array $crumbs): array
    {
        $items = [];
        $pos = 1;

        foreach ($crumbs as $name => $url) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $pos++,
                'name' => $name,
                'item' => $url ? url($url) : url()->current(),
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * Get WebSite schema with search action.
     */
    public static function getWebSiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'LifeGuard Data Recovery Dubai',
            'url' => route('home'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => route('blog.index').'?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Get FAQPage schema.
     */
    public static function getFaqPageSchema(array $faqs): array
    {
        $entities = [];
        foreach ($faqs as $faq) {
            $entities[] = [
                '@type' => 'Question',
                'name' => $faq['question'] ?? '',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'] ?? '',
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $entities,
        ];
    }

    /**
     * Extract FAQs from HTML content matching question headers and paragraphs.
     */
    public static function extractFaqsFromHtml(?string $html): array
    {
        if (! $html) {
            return [];
        }

        $faqs = [];
        if (preg_match_all('/<h[34][^>]*>(.*?\?.*?)<\/h[34]>\s*<p[^>]*>(.*?)<\/p>/is', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $q = trim(strip_tags($match[1]));
                $a = trim(strip_tags($match[2]));
                if ($q && $a) {
                    $faqs[] = ['question' => $q, 'answer' => $a];
                }
            }
        }

        return $faqs;
    }
}

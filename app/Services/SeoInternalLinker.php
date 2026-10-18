<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class SeoInternalLinker
{
    /**
     * Map a blog post to its most relevant LifeGuard Data Recovery service.
     */
    public function matchServiceForPost(Post $post): ?Service
    {
        $services = Service::published()->get();

        if ($services->isEmpty()) {
            return null;
        }

        $categoriesText = $post->relationLoaded('categories')
            ? $post->categories->pluck('name')->join(' ')
            : $post->categories()->pluck('name')->join(' ');

        $haystack = strtolower(
            $post->title.' '.
            $post->slug.' '.
            $categoriesText.' '.
            Str::limit(strip_tags((string) $post->content), 400)
        );

        // 1. CCTV & Surveillance
        if (str_contains($haystack, 'cctv') || str_contains($haystack, 'dvr') || str_contains($haystack, 'nvr') || str_contains($haystack, 'surveillance') || str_contains($haystack, 'footage')) {
            $service = $services->firstWhere('slug', 'cctv-footage-recovery-dubai');
            if ($service) {
                return $service;
            }
        }

        // 2. RAID & Array
        if (str_contains($haystack, 'raid') || str_contains($haystack, 'array') || str_contains($haystack, 'parity') || str_contains($haystack, 'rebuild')) {
            $service = $services->firstWhere('slug', 'raid-data-recovery-dubai');
            if ($service) {
                return $service;
            }
        }

        // 3. NAS Network Attached Storage
        if (str_contains($haystack, 'nas') || str_contains($haystack, 'synology') || str_contains($haystack, 'qnap') || str_contains($haystack, 'btrfs') || str_contains($haystack, 'zfs')) {
            $service = $services->firstWhere('slug', 'nas-data-recovery-dubai');
            if ($service) {
                return $service;
            }
        }

        // 4. Server & Database
        if (str_contains($haystack, 'server') || str_contains($haystack, 'proliant') || str_contains($haystack, 'poweredge') || str_contains($haystack, 'sql') || str_contains($haystack, 'exchange')) {
            $service = $services->firstWhere('slug', 'server-data-recovery-dubai');
            if ($service) {
                return $service;
            }
        }

        // 5. SSD & NVMe Flash
        if (str_contains($haystack, 'ssd') || str_contains($haystack, 'nvme') || str_contains($haystack, 'nand') || str_contains($haystack, 'm.2') || str_contains($haystack, 'solid state')) {
            $service = $services->firstWhere('slug', 'ssd-drive-data-recovery-in-dubai');
            if ($service) {
                return $service;
            }
        }

        // 6. iPhone & Mobile
        if (str_contains($haystack, 'iphone') || str_contains($haystack, 'android') || str_contains($haystack, 'smartphone') || str_contains($haystack, 'mobile')) {
            $service = $services->firstWhere('slug', 'iphone-data-recovery-dubai');
            if ($service) {
                return $service;
            }
        }

        // 7. MacBook & Apple
        if (str_contains($haystack, 'mac') || str_contains($haystack, 'macbook') || str_contains($haystack, 'apple') || str_contains($haystack, 'apfs')) {
            $service = $services->firstWhere('slug', 'mac-data-recovery');
            if ($service) {
                return $service;
            }
        }

        // 8. SD Card & USB
        if (str_contains($haystack, 'sd card') || str_contains($haystack, 'usb') || str_contains($haystack, 'flash drive') || str_contains($haystack, 'pen drive') || str_contains($haystack, 'thumb drive')) {
            $service = $services->firstWhere('slug', 'sd-card-data-recovery');
            if ($service) {
                return $service;
            }
        }

        // 9. Ransomware
        if (str_contains($haystack, 'ransomware') || str_contains($haystack, 'malware') || str_contains($haystack, 'decrypt') || str_contains($haystack, 'virus')) {
            $service = $services->firstWhere('slug', 'ransomware-data-recovery');
            if ($service) {
                return $service;
            }
        }

        // 10. Default to Hard Drive Data Recovery
        return $services->firstWhere('slug', 'hard-drive-data-recovery-dubai') ?: $services->first();
    }

    /**
     * Get related published posts for a data recovery service.
     */
    public function getRelatedPostsForService(Service $service, int $limit = 4): Collection
    {
        $slug = $service->slug;

        $keywords = match ($slug) {
            'cctv-footage-recovery-dubai' => ['cctv', 'footage', 'dvr', 'nvr', 'video'],
            'raid-data-recovery-dubai' => ['raid', 'array', 'server', 'parity', 'rebuild'],
            'nas-data-recovery-dubai' => ['nas', 'synology', 'qnap', 'storage', 'network'],
            'server-data-recovery-dubai' => ['server', 'database', 'sql', 'dell', 'hp'],
            'ssd-drive-data-recovery-in-dubai' => ['ssd', 'nvme', 'flash', 'nand', 'speed'],
            'iphone-data-recovery-dubai' => ['iphone', 'mobile', 'android', 'phone', 'photos'],
            'mac-data-recovery' => ['macbook', 'mac', 'apple', 'apfs', 'laptop'],
            'sd-card-data-recovery' => ['sd', 'card', 'usb', 'camera', 'photos'],
            'ransomware-data-recovery' => ['ransomware', 'malware', 'security', 'backup', 'encrypted'],
            default => ['hard drive', 'hdd', 'crash', 'clicking', 'recovery'],
        };

        return Post::published()
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->orWhere('title', 'like', "%{$kw}%")
                        ->orWhere('slug', 'like', "%{$kw}%");
                }
            })
            ->latest('published_at')
            ->take($limit)
            ->get();
    }
}

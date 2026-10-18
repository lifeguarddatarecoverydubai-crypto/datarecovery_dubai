<?php

namespace App\Models\Concerns;

use App\Support\SeoHelper;
use Illuminate\Support\Facades\Cache;

trait HasFeaturedImage
{
    /**
     * Auto-boot trait to invalidate cached slug routes on save or delete.
     */
    public static function bootHasFeaturedImage(): void
    {
        static::saved(function ($model) {
            if (! empty($model->slug)) {
                Cache::forget("slug_type_{$model->slug}");
            }
        });

        static::deleted(function ($model) {
            if (! empty($model->slug)) {
                Cache::forget("slug_type_{$model->slug}");
            }
        });
    }

    /**
     * Featured image URL accessor.
     * Centralized through SeoHelper to guarantee consistent, deduplicated paths.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        return SeoHelper::featuredImageUrl($this->featured_image);
    }
}

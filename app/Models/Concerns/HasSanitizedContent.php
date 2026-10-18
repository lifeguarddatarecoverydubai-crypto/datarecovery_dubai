<?php

namespace App\Models\Concerns;

use App\Support\ContentHtmlSanitizer;

trait HasSanitizedContent
{
    public function getContentAttribute(?string $value): ?string
    {
        return app(ContentHtmlSanitizer::class)->sanitize($value);
    }

    public function setContentAttribute(?string $value): void
    {
        $this->attributes['content'] = app(ContentHtmlSanitizer::class)->sanitize($value);
    }
}

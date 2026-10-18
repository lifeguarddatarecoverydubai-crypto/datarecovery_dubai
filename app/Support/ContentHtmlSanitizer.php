<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

class ContentHtmlSanitizer
{
    /**
     * @var array<string, array<int, string>>
     */
    private const ALLOWED_TAGS = [
        'a' => ['href', 'title', 'target'],
        'article' => [],
        'aside' => [],
        'b' => [],
        'blockquote' => ['cite'],
        'br' => [],
        'caption' => [],
        'code' => [],
        'del' => [],
        'div' => ['dir', 'style'],
        'em' => [],
        'figure' => [],
        'figcaption' => [],
        'h1' => ['dir', 'style'],
        'h2' => ['dir', 'style'],
        'h3' => ['dir', 'style'],
        'h4' => ['dir', 'style'],
        'h5' => ['dir', 'style'],
        'h6' => ['dir', 'style'],
        'header' => [],
        'hr' => [],
        'i' => [],
        'img' => ['src', 'alt', 'title', 'width', 'height'],
        'li' => ['dir'],
        'main' => [],
        'ol' => ['start', 'type'],
        'p' => ['dir', 'style'],
        'pre' => [],
        's' => [],
        'section' => [],
        'span' => ['dir', 'style'],
        'strong' => [],
        'sub' => [],
        'sup' => [],
        'table' => ['style'],
        'tbody' => [],
        'td' => ['colspan', 'rowspan', 'style'],
        'th' => ['colspan', 'rowspan', 'scope', 'style'],
        'thead' => [],
        'tr' => [],
        'footer' => [],
        'u' => [],
        'ul' => [],
    ];

    /**
     * Sanitize rich content before displaying it or storing it.
     */
    public function sanitize(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previousErrors = libxml_use_internal_errors(true);

        try {
            $document->loadHTML(
                '<?xml encoding="UTF-8"><!DOCTYPE html><html><body><div id="sanitizer-root">'.$html.'</div></body></html>',
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING
            );

            $root = $document->getElementById('sanitizer-root');
            if (! $root) {
                return '';
            }

            $this->sanitizeChildren($root);

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

    private function sanitizeChildren(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $child) {
            if ($child instanceof DOMText) {
                continue;
            }

            if (! $child instanceof DOMElement) {
                $parent->removeChild($child);
                continue;
            }

            $tag = strtolower($child->tagName);
            if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'option', 'svg', 'math', 'template', 'noscript'], true)) {
                $parent->removeChild($child);
                continue;
            }

            $this->sanitizeChildren($child);

            if (! array_key_exists($tag, self::ALLOWED_TAGS)) {
                while ($child->firstChild) {
                    $parent->insertBefore($child->firstChild, $child);
                }
                $parent->removeChild($child);
                continue;
            }

            foreach (iterator_to_array($child->attributes) as $attribute) {
                $name = strtolower($attribute->name);
                if ($name === 'class' && preg_match('/^[\pL\pN_\-:\s]{1,512}$/u', $attribute->value)) {
                    continue;
                }

                if (! in_array($name, self::ALLOWED_TAGS[$tag], true) || str_starts_with($name, 'on')) {
                    $child->removeAttributeNode($attribute);
                    continue;
                }

                if ($name === 'style') {
                    $style = $this->sanitizeStyle($attribute->value);
                    if ($style === '') {
                        $child->removeAttributeNode($attribute);
                    } else {
                        $child->setAttribute('style', $style);
                    }
                    continue;
                }

                if (in_array($name, ['href', 'src', 'cite'], true) && ! $this->isSafeUrl($attribute->value)) {
                    $child->removeAttributeNode($attribute);
                    continue;
                }

                if (in_array($name, ['width', 'height', 'colspan', 'rowspan', 'start'], true)
                    && ! ctype_digit($attribute->value)) {
                    $child->removeAttributeNode($attribute);
                    continue;
                }

                if ($name === 'dir' && ! in_array(strtolower($attribute->value), ['ltr', 'rtl', 'auto'], true)) {
                    $child->removeAttributeNode($attribute);
                }
            }

            if ($tag === 'a' && strtolower($child->getAttribute('target')) === '_blank') {
                $child->setAttribute('rel', 'noopener noreferrer');
            }
        }
    }

    private function isSafeUrl(string $url): bool
    {
        $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($url === '' || preg_match('/[\x00-\x20]/', $url)) {
            return false;
        }

        if (str_starts_with($url, '//')) {
            return false;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);

        return $scheme === null || in_array(strtolower($scheme), ['http', 'https', 'mailto', 'tel'], true);
    }

    private function sanitizeStyle(string $style): string
    {
        $allowedValues = [
            'direction' => ['ltr', 'rtl'],
            'text-align' => ['left', 'right', 'center', 'justify', 'start', 'end'],
        ];
        $safeDeclarations = [];

        foreach (explode(';', $style) as $declaration) {
            $parts = explode(':', $declaration, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $property = strtolower(trim($parts[0]));
            $value = strtolower(trim($parts[1]));
            if (isset($allowedValues[$property]) && in_array($value, $allowedValues[$property], true)) {
                $safeDeclarations[] = $property.': '.$value;
            }
        }

        return implode('; ', $safeDeclarations);
    }
}

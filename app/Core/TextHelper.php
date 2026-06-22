<?php

declare(strict_types=1);

namespace App\Core;

final class TextHelper
{
    public static function sanitizeRichHtml(string $html): string
    {
        // Autorise seulement un sous-ensemble de balises pour conserver la mise en forme.
        $allowed = '<p><br><b><strong><i><em><u><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote>';
        $clean = strip_tags($html, $allowed);

        // Supprime les attributs dangereux comme style ou onclick.
        // $clean = preg_replace('/<(\w+)([^>]*)>/i', function ($matches) {
        //     $tag = $matches[1];
        //     $attrs = $matches[2];
        //     $attrs = preg_replace('/\s*(style|onclick|onmouseover|onerror|onload|formaction)\s*=\s*"[^"]*"/i', '', $attrs);
        //     $attrs = preg_replace('/\s*(style|onclick|onmouseover|onerror|onload|formaction)\s*=\s*\'[^\']*\'/i', '', $attrs);
        //     return '<' . $tag . $attrs . '>';
        // }, $clean);

        return $clean === null ? '' : $clean;
    }
}

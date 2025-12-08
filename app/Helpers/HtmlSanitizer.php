<?php

namespace App\Helpers;

class HtmlSanitizer
{
    /**
     * Sanitize HTML content to prevent XSS while allowing safe HTML tags
     *
     * @param string|null $content
     * @param string $default
     * @return string
     */
    public static function sanitize(?string $content, string $default = ''): string
    {
        if (empty($content)) {
            return $default;
        }

        // Define allowed HTML tags and attributes
        $allowed_tags = '<p><br><strong><em><b><i><h1><h2><h3><h4><h5><h6><ul><ol><li><a><span><div><table><tr><td><th><thead><tbody><tfoot><blockquote><code><pre><img>';

        // First strip all tags that are not in the allowed list
        $sanitized = strip_tags($content, $allowed_tags);

        // Then remove any potentially dangerous attributes (like onclick, javascript:, etc.)
        $sanitized = self::removeDangerousAttributes($sanitized);

        return $sanitized;
    }

    /**
     * Remove dangerous attributes from HTML tags
     *
     * @param string $content
     * @return string
     */
    private static function removeDangerousAttributes(string $content): string
    {
        // Remove dangerous attributes like onclick, onload, etc.
        $pattern = '/(on\w+\s*=|javascript:|vbscript:|data:|<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>)/mi';
        $content = preg_replace($pattern, '', $content);

        // Remove href/src with javascript: or data: protocols
        $content = preg_replace('/(href|src)\s*=\s*["\'](javascript:|vbscript:|data:)[^"\']*/mi', '$1="#"', $content);

        return $content;
    }

    /**
     * Convert Font Awesome icons to Bootstrap Icons for elements that need it
     *
     * @param string|null $content
     * @return string
     */
    public static function convertIcons(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        // Define common Font Awesome to Bootstrap Icons mappings
        $iconMap = [
            'fas fa-shield-alt' => 'bi bi-shield-lock',
            'fas fa-tag' => 'bi bi-tag',
            'fas fa-headset' => 'bi bi-headset',
            'fas fa-map-marker-alt' => 'bi bi-geo-alt',
            'fas fa-phone' => 'bi bi-telephone',
            'fas fa-envelope' => 'bi bi-envelope',
            'fab fa-facebook-f' => 'bi bi-facebook',
            'fab fa-twitter' => 'bi bi-twitter-x',
            'fab fa-instagram' => 'bi bi-instagram',
            'fab fa-youtube' => 'bi bi-youtube',
            'fa-facebook-f' => 'bi bi-facebook',
            'fa-twitter' => 'bi bi-twitter-x',
            'fa-instagram' => 'bi bi-instagram',
            'fa-youtube' => 'bi bi-youtube',
        ];

        // Replace each Font Awesome class with its Bootstrap Icons equivalent
        foreach ($iconMap as $faClass => $biClass) {
            $content = str_replace($faClass, $biClass, $content);
        }

        return $content;
    }
}
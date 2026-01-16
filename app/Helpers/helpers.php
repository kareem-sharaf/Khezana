<?php

/**
 * Global Helper Functions
 *
 * Add custom helper functions here
 */

if (!function_exists('seo')) {
    /**
     * Generate SEO meta tags helper
     *
     * @param array $meta
     * @return array
     */
    function seo(array $meta = []): array
    {
        return array_merge([
            'title' => config('app.name'),
            'description' => '',
            'keywords' => '',
            'og_title' => '',
            'og_description' => '',
            'og_image' => '',
            'og_url' => url()->current(),
        ], $meta);
    }
}

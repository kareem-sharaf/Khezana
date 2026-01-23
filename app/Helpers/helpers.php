<?php

/**
 * Global Helper Functions
 *
 * Add custom helper functions here
 */

if (!function_exists('setting')) {
    /**
     * Get a setting value by key.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('price_with_fee')) {
    /**
     * Apply delivery/service fee to price for display. Donate or null price returns null.
     *
     * @param  float|null  $price
     * @param  string  $operationType  'sell' | 'rent' | 'donate'
     * @return float|null
     */
    function price_with_fee(?float $price, string $operationType): ?float
    {
        if ($price === null || $operationType === 'donate') {
            return null;
        }
        try {
            $percent = \App\Models\Setting::deliveryServiceFeePercent();
        } catch (\Throwable $e) {
            return round($price, 2);
        }

        return round($price * (1 + $percent / 100), 2);
    }
}

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

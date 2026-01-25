<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'string',
    ];

    private const CACHE_KEY_PREFIX = 'khezana_setting:';
    private const CACHE_TTL_SECONDS = 3600;

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = self::CACHE_KEY_PREFIX . $key;

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($key, $default) {
            $row = self::where('key', $key)->first();

            return $row !== null ? $row->value : $default;
        });
    }

    /**
     * Set a setting value. Creates or updates the row.
     */
    public static function set(string $key, mixed $value): void
    {
        $value = is_scalar($value) ? (string) $value : json_encode($value);
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        Cache::forget(self::CACHE_KEY_PREFIX . $key);
    }

    /**
     * Get delivery/service fee percent (0–100). Used for display and pre-creation notice.
     */
    public static function deliveryServiceFeePercent(): float
    {
        $v = (float) self::get('delivery_service_fee_percent', 10);

        return max(0, min(100, $v));
    }

    /**
     * Get price slider minimum value.
     */
    public static function priceSliderMin(): float
    {
        return max(0, (float) self::get('price_slider_min', 0));
    }

    /**
     * Get price slider maximum value.
     * From settings; safe fallback only when not yet configured.
     */
    public static function priceSliderMax(): float
    {
        $value = self::get('price_slider_max');
        if ($value === null || $value === '') {
            return 1000000.0; // fallback until admin sets it
        }
        return max(1, (float) $value);
    }

    /**
     * Get price slider step value.
     * From settings; safe fallback only when not yet configured.
     */
    public static function priceSliderStep(): float
    {
        $value = self::get('price_slider_step');
        if ($value === null || $value === '') {
            return 1000.0; // fallback until admin sets it
        }
        return max(1, (float) $value);
    }

    /**
     * Get price slider minimum gap between min and max.
     * From settings; safe fallback only when not yet configured.
     */
    public static function priceSliderMinGap(): float
    {
        $value = self::get('price_slider_min_gap');
        if ($value === null || $value === '') {
            return 1000.0; // fallback until admin sets it
        }
        return max(0, (float) $value);
    }
}

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
}

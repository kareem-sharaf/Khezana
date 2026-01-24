<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Read\Items\Models\ItemReadModel;
use App\Read\Items\Queries\BrowseItemsQuery;
use App\Read\Requests\Queries\BrowseRequestsQuery;
use App\Read\Requests\Models\RequestReadModel;
use App\Services\Cache\CacheService;
use App\Services\Cache\CategoryCacheService;
use Illuminate\Console\Command;

/**
 * Phase 6.1: Warm cache for common pages (categories, items index, requests index)
 *
 * Usage: php artisan cache:warm
 */
class WarmCacheCommand extends Command
{
    protected $signature = 'cache:warm
                            {--locales=ar,en : Comma-separated locales to warm}
                            {--clear : Clear cache before warming}';

    protected $description = 'Warm cache for categories, items index, and requests index';

    public function handle(
        CacheService $cache,
        CategoryCacheService $categoryCache,
        BrowseItemsQuery $browseItems,
        BrowseRequestsQuery $browseRequests,
    ): int {
        if ($this->option('clear')) {
            $this->info('Clearing cache…');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
        }

        $locales = array_filter(array_map('trim', explode(',', $this->option('locales'))));

        $this->info('Warming categories…');
        $categoryCache->getTree();
        $this->line('   Categories OK');

        foreach ($locales as $locale) {
            app()->setLocale($locale);
            $this->info("Warming items index (locale: {$locale})…");
            $cache->rememberItemsIndex(
                fn () => $browseItems->execute([], null, 1, 9, null)->through(fn ($i) => ItemReadModel::fromModel($i)),
                [],
                'created_at_desc',
                1,
                $locale,
                null,
                9,
            );
            $this->line("   Items index OK");

            $this->info("Warming requests index (locale: {$locale})…");
            $cache->rememberRequestsIndex(
                fn () => $browseRequests->execute([], 'created_at_desc', 1, 9)->through(fn ($r) => RequestReadModel::fromModel($r)),
                [],
                'created_at_desc',
                1,
                $locale,
            );
            $this->line("   Requests index OK");
        }

        $this->info('Cache warm-up complete.');
        return self::SUCCESS;
    }
}

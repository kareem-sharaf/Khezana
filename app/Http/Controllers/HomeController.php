<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Read\Items\Models\ItemReadModel;
use App\Read\Items\Queries\BrowseItemsQuery;
use App\Services\Cache\CacheService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly BrowseItemsQuery $browseItemsQuery,
        private readonly CacheService $cacheService,
    ) {}

    public function index(Request $request): View
    {
        $locale = app()->getLocale();

        // Check if user is new visitor (first time on home page)
        $isNewVisitor = !$request->session()->has('visited_home');
        if ($isNewVisitor) {
            $request->session()->put('visited_home', true);
        }

        // Get featured items for each operation type (cached)
        $featuredSell = \Illuminate\Support\Facades\Cache::remember(
            'home_featured_sell_' . $locale,
            300, // 5 minutes
            fn() => $this->getFeaturedItems('sell')
        );

        $featuredRent = \Illuminate\Support\Facades\Cache::remember(
            'home_featured_rent_' . $locale,
            300,
            fn() => $this->getFeaturedItems('rent')
        );

        $featuredDonate = \Illuminate\Support\Facades\Cache::remember(
            'home_featured_donate_' . $locale,
            300,
            fn() => $this->getFeaturedItems('donate')
        );

        return view('home.index', [
            'featuredSell' => $featuredSell,
            'featuredRent' => $featuredRent,
            'featuredDonate' => $featuredDonate,
            'isNewVisitor' => $isNewVisitor,
        ]);
    }

    private function getFeaturedItems(string $operationType): \Illuminate\Support\Collection
    {
        $filters = ['operation_type' => $operationType];
        $sort = 'created_at_desc';

        $itemsPaginator = $this->browseItemsQuery->execute($filters, $sort, 1, 6);

        return collect($itemsPaginator->items())->map(fn($item) => ItemReadModel::fromModel($item));
    }
}

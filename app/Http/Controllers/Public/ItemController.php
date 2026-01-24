<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Read\Items\Models\ItemReadModel;
use App\Read\Items\Queries\BrowseItemsQuery;
use App\Read\Items\Queries\SimilarItemsQuery;
use App\Read\Items\Queries\ViewItemQuery;
use App\Services\Cache\CacheService;
use App\Services\Cache\CategoryCacheService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct(
        private readonly BrowseItemsQuery $browseItemsQuery,
        private readonly ViewItemQuery $viewItemQuery,
        private readonly SimilarItemsQuery $similarItemsQuery,
        private readonly CacheService $cacheService,
        private readonly CategoryCacheService $categoryCacheService,
    ) {}

    public function index(Request $request): View
    {
        $sort = $request->get('sort', 'created_at_desc');
        $page = max(1, (int) $request->get('page', 1));
        $perPage = min(50, max(1, (int) $request->get('per_page', 20)));
        $locale = app()->getLocale();

        // Extract filters from request
        $filters = [
            'operation_type' => $request->get('operation_type'),
            'category_id' => $request->get('category_id'),
            'condition' => $request->get('condition'),
            'price_min' => $request->get('price_min'),
            'price_max' => $request->get('price_max'),
            'search' => $request->get('search'),
        ];

        // Remove empty filters
        $filters = array_filter($filters, fn($value) => $value !== null && $value !== '');

        $user = $request->user();

        $items = $this->cacheService->rememberItemsIndex(
            function () use ($filters, $sort, $page, $perPage, $user) {
                $itemsPaginator = $this->browseItemsQuery->execute($filters, $sort, $page, $perPage, $user);
                return $itemsPaginator->through(fn($item) => ItemReadModel::fromModel($item));
            },
            $filters,
            $sort,
            $page,
            $locale,
            $user?->id
        );

        // Ensure pagination preserves all query parameters (filters, sort, etc.)
        $items->appends($request->query());

        // Get categories for filter dropdown
        $categories = $this->categoryCacheService->getTree();

        // Calculate active filters count for badge
        $activeFiltersCount = count($filters);

        return view('public.items.index', [
            'items' => $items,
            'sort' => $sort,
            'filters' => $filters,
            'categories' => $categories,
            'activeFiltersCount' => $activeFiltersCount,
        ]);
    }

    public function show(Request $request, int $id, ?string $slug = null): View|RedirectResponse
    {
        $user = $request->user();
        $locale = app()->getLocale();

        $item = $this->cacheService->rememberItemShow(
            function () use ($id, $slug, $user) {
                return $this->viewItemQuery->execute($id, $slug, $user);
            },
            $id,
            $slug,
            $user?->id,
            $locale
        );

        if (!$item) {
            abort(404, 'Item not found or not visible.');
        }

        if ($slug && $item->slug !== $slug) {
            return redirect()->route('public.items.show', ['id' => $item->id, 'slug' => $item->slug], 301);
        }

        $viewModel = \App\ViewModels\Items\ItemDetailViewModel::fromItem($item, 'public');

        // Get similar items (same category, same operation type, same condition if available)
        $similarItems = $this->similarItemsQuery->execute(
            $item->id,
            $item->category?->id,
            $item->operationType,
            $item->condition,
            12, // Limit to 12 items
            $user
        );

        return view('public.items.show', [
            'viewModel' => $viewModel,
            'similarItems' => $similarItems,
        ]);
    }

    public function contact(Request $request, int $id): RedirectResponse
    {
        $item = \App\Models\Item::findOrFail($id);

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // TODO: Send contact message (email/notification)

        return redirect()->route('public.items.show', ['id' => $item->id, 'slug' => $item->slug])
            ->with('success', 'تم إرسال رسالتك بنجاح. سيتم التواصل معك قريباً.');
    }

    public function report(Request $request, int $id): RedirectResponse
    {
        $item = \App\Models\Item::findOrFail($id);

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // TODO: Create report record

        return redirect()->route('public.items.show', ['id' => $item->id, 'slug' => $item->slug])
            ->with('success', 'تم الإبلاغ عن الإعلان. شكراً لك.');
    }
}

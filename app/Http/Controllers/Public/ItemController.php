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

    public function index(\App\Http\Requests\FilterItemsRequest $request): View|RedirectResponse
    {
        $sort = $request->validated()['sort'] ?? 'created_at_desc';
        $page = max(1, (int) ($request->validated()['page'] ?? 1));
        $perPage = min(50, max(1, (int) ($request->validated()['per_page'] ?? 10)));
        $locale = app()->getLocale();

        // Performance fix #17: Build filters array with filtering in one step
        $filters = array_filter([
            'operation_type' => $request->validated()['operation_type'] ?? null,
            'category_id' => $request->validated()['category_id'] ?? null,
            'condition' => $request->validated()['condition'] ?? null,
            'price_min' => $request->validated()['price_min'] ?? null,
            'price_max' => $request->validated()['price_max'] ?? null,
            'search' => $request->validated()['search'] ?? null,
        ], fn($value) => $value !== null && $value !== '');

        $user = $request->user();

        // Performance fix #9: Remove userId from cache key - use same cache for all users
        $items = $this->cacheService->rememberItemsIndex(
            function () use ($filters, $sort, $page, $perPage, $user) {
                $itemsPaginator = $this->browseItemsQuery->execute($filters, $sort, $page, $perPage, $user);
                return $itemsPaginator->through(fn($item) => ItemReadModel::fromModel($item));
            },
            $filters,
            $sort,
            $page,
            $locale,
            null, // Performance fix: Don't include userId in cache key
            $perPage // Include per_page in cache key
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

    public function contact(Request $request, \App\Models\Item $item): RedirectResponse
    {
        // Verify item is available and approved
        if (!$item->isApproved() || !$item->is_available || $item->deleted_at) {
            abort(404, __('items.messages.item_not_available'));
        }

        // Prevent users from contacting their own items
        if ($request->user() && $item->user_id === $request->user()->id) {
            return back()->withErrors(['error' => __('items.messages.cannot_contact_own_item')]);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Send contact message
        try {
            \App\Jobs\SendItemContactMessageJob::dispatch(
                $item,
                $validated['name'],
                $validated['email'],
                $validated['message']
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to dispatch item contact message job', [
                'item_id' => $item->id,
                'error' => $e->getMessage(),
            ]);
            return back()->withErrors(['error' => __('items.messages.contact_send_failed')]);
        }

        return redirect()->route('public.items.show', ['id' => $item->id, 'slug' => $item->slug])
            ->with('success', __('items.messages.contact_sent_successfully'));
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

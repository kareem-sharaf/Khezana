<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Read\Items\Models\ItemReadModel;
use App\Read\Items\Queries\BrowseItemsQuery;
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
        private readonly CacheService $cacheService,
        private readonly CategoryCacheService $categoryCacheService,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'operation_type' => $request->get('operation_type'),
            'category_id' => $request->get('category_id') ? (int) $request->get('category_id') : null,
            'price_min' => $request->get('price_min') ? (float) $request->get('price_min') : null,
            'price_max' => $request->get('price_max') ? (float) $request->get('price_max') : null,
        ];

        $sort = $request->get('sort', 'created_at_desc');
        $page = max(1, (int) $request->get('page', 1));
        $perPage = min(50, max(1, (int) $request->get('per_page', 20)));
        $locale = app()->getLocale();

        $items = $this->cacheService->rememberItemsIndex(
            function () use ($filters, $sort, $page, $perPage) {
                $itemsPaginator = $this->browseItemsQuery->execute($filters, $sort, $page, $perPage);
                return $itemsPaginator->through(fn($item) => ItemReadModel::fromModel($item));
            },
            $filters,
            $sort,
            $page,
            $locale
        );

        $categories = $this->categoryCacheService->getTree();

        return view('public.items.index', [
            'items' => $items,
            'filters' => $filters,
            'sort' => $sort,
            'categories' => $categories,
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

        return view('public.items.show', [
            'item' => $item,
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

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Read\Items\Models\ItemReadModel;
use App\Read\Items\Queries\BrowseItemsQuery;
use App\Read\Items\Queries\ViewItemQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct(
        private readonly BrowseItemsQuery $browseItemsQuery,
        private readonly ViewItemQuery $viewItemQuery,
    ) {
    }

    public function index(Request $request): View
    {
        $filters = [
            'operation_type' => $request->get('operation_type'),
            'category_id' => $request->get('category_id') ? (int) $request->get('category_id') : null,
            'price_min' => $request->get('price_min') ? (float) $request->get('price_min') : null,
            'price_max' => $request->get('price_max') ? (float) $request->get('price_max') : null,
        ];

        $sort = $request->get('sort', 'created_at_desc');
        $page = max(1, (int) $request->get('page', 1));
        $perPage = min(50, max(1, (int) $request->get('per_page', 20)));

        $itemsPaginator = $this->browseItemsQuery->execute($filters, $sort, $page, $perPage);

        $items = $itemsPaginator->through(fn($item) => ItemReadModel::fromModel($item));

        return view('public.items.index', [
            'items' => $items,
            'filters' => $filters,
            'sort' => $sort,
        ]);
    }

    public function show(Request $request, int $id, ?string $slug = null): View|RedirectResponse
    {
        $user = $request->user();

        $item = $this->viewItemQuery->execute($id, $slug, $user);

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
}

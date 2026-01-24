<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Item\CreateItemAction;
use App\Actions\Item\DeleteItemAction;
use App\Actions\Item\SubmitItemForApprovalAction;
use App\Actions\Item\UpdateItemAction;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Setting;
use App\Services\Cache\CategoryCacheService;
use App\Services\ImageOptimizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Item Controller
 *
 * Handles user-facing item operations
 * All business logic is delegated to Actions
 */
class ItemController extends Controller
{
    public function __construct(
        private readonly CreateItemAction $createItemAction,
        private readonly UpdateItemAction $updateItemAction,
        private readonly DeleteItemAction $deleteItemAction,
        private readonly SubmitItemForApprovalAction $submitForApprovalAction,
        private readonly ImageOptimizationService $imageService,
        private readonly CategoryCacheService $categoryCacheService
    ) {
        // Middleware is applied in routes/web.php
    }

    /**
     * Display a listing of user's items
     */
    public function index(Request $request): View
    {
        $sort = $request->get('sort', 'created_at_desc');
        $page = max(1, (int) $request->get('page', 1));
        $perPage = min(50, max(1, (int) $request->get('per_page', 12)));

        $query = Item::where('user_id', Auth::id())
            ->with(['category', 'images', 'approvalRelation']);

        // Apply filters (Phase 2.1: use Full-Text search when available)
        if ($request->filled('search')) {
            $query->search($request->get('search'));
        }

        if ($request->has('category_id') && $request->get('category_id')) {
            $query->where('category_id', (int) $request->get('category_id'));
        }

        if ($request->has('condition') && $request->get('condition')) {
            $query->where('condition', $request->get('condition'));
        }

        if ($request->has('operation_type') && $request->get('operation_type')) {
            $query->where('operation_type', $request->get('operation_type'));
        }

        if ($request->has('price_min') && $request->get('price_min')) {
            $query->where('price', '>=', (float) $request->get('price_min'));
        }

        if ($request->has('price_max') && $request->get('price_max')) {
            $query->where('price', '<=', (float) $request->get('price_max'));
        }

        // Apply sorting
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'title_asc' => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            'created_at_asc' => $query->orderBy('created_at', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $items = $query->paginate($perPage, ['*'], 'page', $page);
        $items->appends($request->query());

        // Extract filters for view
        $filters = [
            'search' => $request->get('search'),
            'category_id' => $request->get('category_id'),
            'condition' => $request->get('condition'),
            'operation_type' => $request->get('operation_type'),
            'price_min' => $request->get('price_min'),
            'price_max' => $request->get('price_max'),
        ];
        $filters = array_filter($filters, fn($value) => $value !== null && $value !== '');

        // Get categories for filter dropdown
        $categories = Category::active()
            ->with(['children' => fn($q) => $q->where('is_active', true)->orderBy('name')])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        // Calculate active filters count for badge
        $activeFiltersCount = count($filters);

        return view('items.index', compact('items', 'sort', 'filters', 'categories', 'activeFiltersCount'));
    }

    /**
     * Show the form for creating a new item
     */
    public function create(): View
    {
        // Phase 1.2: Use cached categories (TTL: 1 hour)
        $categories = $this->categoryCacheService->getTree();
        
        $feePercent = (float) Setting::deliveryServiceFeePercent();
        $preCreationRules = [
            ['icon' => '💰', 'text' => __('items.pre_creation_notice.rule_fee', ['percent' => (string) (int) $feePercent])],
            ['icon' => '📞', 'text' => __('items.pre_creation_notice.rule_contact')],
        ];

        return view('items.create', compact('categories', 'feePercent', 'preCreationRules'));
    }

    /**
     * Store a newly created item
     */
    public function store(StoreItemRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            // Handle image uploads - Store in filesystem
            $imageData = [];
            if ($request->hasFile('images')) {
                // We'll get item ID after creation, so we'll process images in the Action
                // For now, just pass the files
                $imageData = $request->file('images');
            }

            $item = $this->createItemAction->execute(
                $validated,
                Auth::user(),
                $validated['attributes'] ?? null,
                !empty($imageData) ? $imageData : null
            );

            return redirect()->route('items.show', $item)
                ->with('success', __('items.messages.created'));
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified item
     */
    public function show(Item $item): View
    {
        // Check authorization
        $this->authorize('view', $item);

        $item->load(['user', 'category', 'images', 'itemAttributes.attribute', 'approvalRelation']);

        $viewModel = \App\ViewModels\Items\ItemDetailViewModel::fromItem($item, 'user');

        return view('items.show', ['viewModel' => $viewModel]);
    }

    /**
     * Show the form for editing the specified item
     */
    public function edit(Item $item): View
    {
        $this->authorize('update', $item);

        $categories = Category::active()
            ->with(['children' => fn($q) => $q->where('is_active', true)->orderBy('name')])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
        $item->load(['category', 'images', 'itemAttributes.attribute']);

        return view('items.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified item
     */
    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        $this->authorize('update', $item);

        try {
            $validated = $request->validated();

            // Handle image uploads - Store in filesystem
            $imageData = null;
            if ($request->hasFile('images')) {
                // Pass files to Action for processing
                $imageData = $request->file('images');
            }

            $item = $this->updateItemAction->execute(
                $item,
                $validated,
                $validated['attributes'] ?? null,
                $imageData
            );

            return redirect()->route('items.show', $item)
                ->with('success', __('items.messages.updated'));
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified item (soft delete)
     */
    public function destroy(Request $request, Item $item): RedirectResponse
    {
        $this->authorize('delete', $item);

        try {
            $user = Auth::user();
            $reason = $request->input('reason');
            $archive = $request->boolean('archive', false);

            // Admin must provide reason
            if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'super_admin']) && empty($reason)) {
                return back()->withErrors(['reason' => __('items.deletion.reason_required')]);
            }

            $this->deleteItemAction->softDelete($item, $user, $reason, $archive);

            $message = $archive
                ? __('items.messages.archived')
                : __('items.messages.deleted');

            return redirect()->route('items.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Hard delete an item (super admin only)
     * Requires confirmation text "DELETE"
     */
    public function forceDestroy(Request $request, Item $item): RedirectResponse
    {
        $this->authorize('hardDelete', $item);

        $user = Auth::user();
        $reason = $request->input('reason');
        $confirmation = $request->input('confirmation');

        // Validate confirmation
        if ($confirmation !== 'DELETE') {
            return back()->withErrors([
                'confirmation' => __('items.deletion.confirmation_required')
            ]);
        }

        // Reason is required
        if (empty($reason)) {
            return back()->withErrors(['reason' => __('items.deletion.reason_required')]);
        }

        try {
            $this->deleteItemAction->hardDelete($item, $user, $reason);

            return redirect()->route('items.index')
                ->with('success', __('items.messages.permanently_deleted'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Restore a soft-deleted item (admin only)
     */
    public function restore(Item $item): RedirectResponse
    {
        $this->authorize('restore', $item);

        try {
            $item->restore();

            // Clear archived_at if it exists
            if ($item->archived_at) {
                $item->update(['archived_at' => null]);
            }

            return redirect()->route('items.show', $item)
                ->with('success', __('items.messages.restored'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Submit item for approval
     */
    public function submitForApproval(Item $item): RedirectResponse
    {
        $this->authorize('submitForApproval', $item);

        try {
            $this->submitForApprovalAction->execute($item, Auth::user());

            return redirect()->route('items.show', $item)
                ->with('success', __('items.messages.submitted_for_approval'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

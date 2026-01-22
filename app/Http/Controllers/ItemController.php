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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        private readonly SubmitItemForApprovalAction $submitForApprovalAction
    ) {
        // Middleware is applied in routes/web.php
    }

    /**
     * Display a listing of user's items
     */
    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'operation_type' => $request->get('operation_type'),
            'category_id' => $request->get('category_id') ? (int) $request->get('category_id') : null,
            'approval_status' => $request->get('approval_status'),
        ];

        $sort = $request->get('sort', 'created_at_desc');
        $page = max(1, (int) $request->get('page', 1));
        $perPage = min(50, max(1, (int) $request->get('per_page', 12)));

        $query = Item::where('user_id', Auth::id())
            ->with(['category', 'images', 'approvalRelation']);

        // Apply filters
        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['operation_type']) && $filters['operation_type']) {
            $query->where('operation_type', $filters['operation_type']);
        }

        if (isset($filters['category_id']) && $filters['category_id']) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['approval_status']) && $filters['approval_status']) {
            $query->whereHas('approvalRelation', fn($q) => $q->where('status', $filters['approval_status']));
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

        $categories = Category::active()->get();

        return view('items.index', compact('items', 'filters', 'sort', 'categories'));
    }

    /**
     * Show the form for creating a new item
     */
    public function create(): View
    {
        $categories = Category::active()->get();
        return view('items.create', compact('categories'));
    }

    /**
     * Store a newly created item
     */
    public function store(StoreItemRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            
            $item = $this->createItemAction->execute(
                $validated,
                Auth::user(),
                $validated['attributes'] ?? null,
                $validated['images'] ?? null
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

        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified item
     */
    public function edit(Item $item): View
    {
        $this->authorize('update', $item);

        $categories = Category::active()->get();
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
            
            $item = $this->updateItemAction->execute(
                $item,
                $validated,
                $validated['attributes'] ?? null,
                $validated['images'] ?? null
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

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Item\CreateItemAction;
use App\Actions\Item\DeleteItemAction;
use App\Actions\Item\SubmitItemForApprovalAction;
use App\Actions\Item\UpdateItemAction;
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
    public function index(): View
    {
        $items = Item::where('user_id', Auth::id())
            ->with(['category', 'images', 'approvalRelation'])
            ->latest()
            ->paginate(12);
        // Note: Soft deleted items are shown to their owners for restoration purposes

        return view('items.index', compact('items'));
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
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'operation_type' => 'required|in:sell,rent,donate',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'is_available' => 'boolean',
            'attributes' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'string',
        ]);

        try {
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
    public function update(Request $request, Item $item): RedirectResponse
    {
        $this->authorize('update', $item);

        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'operation_type' => 'sometimes|in:sell,rent,donate',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'is_available' => 'boolean',
            'attributes' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'string',
        ]);

        try {
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

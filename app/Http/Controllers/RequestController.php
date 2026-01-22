<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Request\CloseRequestAction;
use App\Actions\Request\CreateRequestAction;
use App\Actions\Request\DeleteRequestAction;
use App\Actions\Request\SubmitRequestForApprovalAction;
use App\Actions\Request\UpdateRequestAction;
use App\Models\Category;
use App\Models\Request as RequestModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Request Controller
 *
 * Handles user-facing request operations
 * All business logic is delegated to Actions
 */
class RequestController extends Controller
{
    public function __construct(
        private readonly CreateRequestAction $createRequestAction,
        private readonly UpdateRequestAction $updateRequestAction,
        private readonly DeleteRequestAction $deleteRequestAction,
        private readonly SubmitRequestForApprovalAction $submitForApprovalAction,
        private readonly CloseRequestAction $closeRequestAction
    ) {
        // Middleware is applied in routes/web.php
    }

    /**
     * Display a listing of user's requests
     */
    public function index(HttpRequest $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'category_id' => $request->get('category_id') ? (int) $request->get('category_id') : null,
            'approval_status' => $request->get('approval_status'),
        ];

        $sort = $request->get('sort', 'created_at_desc');
        $page = max(1, (int) $request->get('page', 1));
        $perPage = min(50, max(1, (int) $request->get('per_page', 12)));

        $query = RequestModel::where('user_id', Auth::id())
            ->with(['category', 'approvalRelation', 'itemAttributes.attribute', 'offers']);

        // Apply filters
        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category_id']) && $filters['category_id']) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['approval_status']) && $filters['approval_status']) {
            $query->whereHas('approvalRelation', fn($q) => $q->where('status', $filters['approval_status']));
        }

        // Apply sorting
        match($sort) {
            'status_asc' => $query->orderBy('status', 'asc'),
            'status_desc' => $query->orderBy('status', 'desc'),
            'title_asc' => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            'created_at_asc' => $query->orderBy('created_at', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $requests = $query->paginate($perPage, ['*'], 'page', $page);
        $requests->appends($request->query());

        $categories = Category::active()->get();

        return view('requests.index', compact('requests', 'filters', 'sort', 'categories'));
    }

    /**
     * Show the form for creating a new request
     */
    public function create(): View
    {
        $categories = Category::active()
            ->with(['attributes.values', 'children.attributes.values'])
            ->whereNull('parent_id')
            ->get();

        return view('requests.create', compact('categories'));
    }

    /**
     * Store a newly created request
     */
    public function store(HttpRequest $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attributes' => 'nullable|array',
        ]);

        $requestModel = $this->createRequestAction->execute(
            $validated,
            Auth::user(),
            $validated['attributes'] ?? null
        );

        return redirect()->route('requests.show', $requestModel)
            ->with('success', __('requests.messages.created_successfully'));
    }

    /**
     * Display the specified request
     */
    public function show(RequestModel $request): View
    {
        $this->authorize('view', $request);

        $request->load([
            'user',
            'category',
            'itemAttributes.attribute',
            'approvalRelation',
            'offers.user',
            'offers.item'
        ]);

        return view('requests.show', ['requestModel' => $request]);
    }

    /**
     * Show the form for editing the specified request
     */
    public function edit(RequestModel $request): View
    {
        $this->authorize('update', $request);

        // Cannot edit closed or fulfilled requests
        if ($request->isClosed() || $request->isFulfilled()) {
            abort(403, __('requests.messages.cannot_edit_closed'));
        }

        $categories = Category::with('attributes')->get();
        $request->load('itemAttributes.attribute');

        return view('requests.edit', ['requestModel' => $request, 'categories' => $categories]);
    }

    /**
     * Update the specified request
     */
    public function update(HttpRequest $request, RequestModel $requestModel): RedirectResponse
    {
        $this->authorize('update', $requestModel);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attributes' => 'nullable|array',
        ]);

        $this->updateRequestAction->execute(
            $requestModel,
            $validated,
            $validated['attributes'] ?? null
        );

        return redirect()->route('requests.show', $requestModel)
            ->with('success', __('requests.messages.updated_successfully'));
    }

    /**
     * Remove the specified request (soft delete)
     */
    public function destroy(HttpRequest $request, RequestModel $requestModel): RedirectResponse
    {
        $this->authorize('delete', $requestModel);

        try {
            $user = Auth::user();
            $reason = $request->input('reason');
            $archive = $request->boolean('archive', false);

            // Admin must provide reason
            if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'super_admin']) && empty($reason)) {
                return back()->withErrors(['reason' => __('requests.deletion.reason_required')]);
            }

            $this->deleteRequestAction->softDelete($requestModel, $user, $reason, $archive);

            $message = $archive
                ? __('requests.messages.archived')
                : __('requests.messages.deleted_successfully');

            return redirect()->route('requests.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Hard delete a request (super admin only)
     * Requires confirmation text "DELETE"
     */
    public function forceDestroy(HttpRequest $request, RequestModel $requestModel): RedirectResponse
    {
        $this->authorize('hardDelete', $requestModel);

        $user = Auth::user();
        $reason = $request->input('reason');
        $confirmation = $request->input('confirmation');

        // Validate confirmation
        if ($confirmation !== 'DELETE') {
            return back()->withErrors([
                'confirmation' => __('requests.deletion.confirmation_required')
            ]);
        }

        // Reason is required
        if (empty($reason)) {
            return back()->withErrors(['reason' => __('requests.deletion.reason_required')]);
        }

        try {
            $this->deleteRequestAction->hardDelete($requestModel, $user, $reason);

            return redirect()->route('requests.index')
                ->with('success', __('requests.messages.permanently_deleted'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Restore a soft-deleted request (admin only)
     */
    public function restore(RequestModel $requestModel): RedirectResponse
    {
        $this->authorize('restore', $requestModel);

        try {
            $requestModel->restore();

            // Clear archived_at if it exists
            if ($requestModel->archived_at) {
                $requestModel->update(['archived_at' => null]);
            }

            return redirect()->route('requests.show', $requestModel)
                ->with('success', __('requests.messages.restored'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Submit request for approval
     */
    public function submitForApproval(RequestModel $requestModel): RedirectResponse
    {
        $this->authorize('submitForApproval', $requestModel);

        $this->submitForApprovalAction->execute($requestModel, Auth::user());

        return redirect()->route('requests.show', $requestModel)
            ->with('success', __('requests.messages.submitted_for_approval'));
    }

    /**
     * Close the specified request
     */
    public function close(RequestModel $requestModel): RedirectResponse
    {
        $this->authorize('close', $requestModel);

        $this->closeRequestAction->execute($requestModel, Auth::user());

        return redirect()->route('requests.show', $requestModel)
            ->with('success', __('requests.messages.closed_successfully'));
    }
}

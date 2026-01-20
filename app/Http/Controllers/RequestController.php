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
        $this->middleware('auth');
    }

    /**
     * Display a listing of user's requests
     */
    public function index(): View
    {
        $requests = RequestModel::where('user_id', Auth::id())
            ->with(['category', 'approvalRelation'])
            ->latest()
            ->paginate(12);

        return view('requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new request
     */
    public function create(): View
    {
        $categories = Category::with('attributes')->get();
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
    public function show(RequestModel $requestModel): View
    {
        $this->authorize('view', $requestModel);

        $requestModel->load([
            'user',
            'category',
            'itemAttributes.attribute',
            'approvalRelation',
            'offers.user',
            'offers.item'
        ]);

        return view('requests.show', compact('requestModel'));
    }

    /**
     * Show the form for editing the specified request
     */
    public function edit(RequestModel $requestModel): View
    {
        $this->authorize('update', $requestModel);

        // Cannot edit closed or fulfilled requests
        if ($requestModel->isClosed() || $requestModel->isFulfilled()) {
            abort(403, __('requests.messages.cannot_edit_closed'));
        }

        $categories = Category::with('attributes')->get();
        $requestModel->load('itemAttributes.attribute');

        return view('requests.edit', compact('requestModel', 'categories'));
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
     * Remove the specified request
     */
    public function destroy(RequestModel $requestModel): RedirectResponse
    {
        $this->authorize('delete', $requestModel);

        $this->deleteRequestAction->execute($requestModel);

        return redirect()->route('requests.index')
            ->with('success', __('requests.messages.deleted_successfully'));
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

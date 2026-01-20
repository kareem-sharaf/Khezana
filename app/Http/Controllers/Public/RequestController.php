<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Read\Requests\Models\RequestReadModel;
use App\Read\Requests\Queries\BrowseRequestsQuery;
use App\Read\Requests\Queries\ViewRequestQuery;
use App\Services\Cache\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function __construct(
        private readonly BrowseRequestsQuery $browseRequestsQuery,
        private readonly ViewRequestQuery $viewRequestQuery,
        private readonly CacheService $cacheService,
    ) {
    }

    public function createInfo(): View
    {
        return view('public.requests.create-info');
    }

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'category_id' => $request->get('category_id') ? (int) $request->get('category_id') : null,
        ];

        $sort = $request->get('sort', 'created_at_desc');
        $page = max(1, (int) $request->get('page', 1));
        $perPage = min(50, max(1, (int) $request->get('per_page', 20)));
        $locale = app()->getLocale();

        $requests = $this->cacheService->rememberRequestsIndex(
            function () use ($filters, $sort, $page, $perPage) {
                $requestsPaginator = $this->browseRequestsQuery->execute($filters, $sort, $page, $perPage);
                return $requestsPaginator->through(fn($request) => RequestReadModel::fromModel($request));
            },
            $filters,
            $sort,
            $page,
            $locale
        );

        return view('public.requests.index', [
            'requests' => $requests,
            'filters' => $filters,
            'sort' => $sort,
        ]);
    }

    public function show(Request $request, int $id, ?string $slug = null): View|RedirectResponse
    {
        $user = $request->user();
        $locale = app()->getLocale();

        $requestModel = $this->cacheService->rememberRequestShow(
            function () use ($id, $slug, $user) {
                return $this->viewRequestQuery->execute($id, $slug, $user);
            },
            $id,
            $slug,
            $user?->id,
            $locale
        );

        if (!$requestModel) {
            abort(404, 'Request not found or not visible.');
        }

        if ($slug && $requestModel->slug !== $slug) {
            return redirect()->route('public.requests.show', ['id' => $requestModel->id, 'slug' => $requestModel->slug], 301);
        }

        return view('public.requests.show', [
            'request' => $requestModel,
        ]);
    }

    public function submitOffer(Request $request, int $id): RedirectResponse
    {
        $requestModel = \App\Models\Request::findOrFail($id);
        
        $validated = $request->validate([
            'operation_type' => 'required|in:sell,rent,donate',
            'price' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'message' => 'nullable|string|max:1000',
            'item_id' => 'nullable|exists:items,id',
        ]);

        $offer = \App\Actions\Offer\CreateOfferAction::class;
        $createOfferAction = app(\App\Actions\Offer\CreateOfferAction::class);
        
        $offer = $createOfferAction->execute(
            $validated,
            $requestModel,
            $request->user()
        );

        return redirect()->route('public.requests.show', ['id' => $requestModel->id, 'slug' => $requestModel->slug])
            ->with('success', 'تم تقديم عرضك بنجاح.');
    }

    public function contact(Request $request, int $id): RedirectResponse
    {
        $requestModel = \App\Models\Request::findOrFail($id);
        
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // TODO: Send contact message
        
        return redirect()->route('public.requests.show', ['id' => $requestModel->id, 'slug' => $requestModel->slug])
            ->with('success', 'تم إرسال رسالتك بنجاح. سيتم التواصل معك قريباً.');
    }

    public function report(Request $request, int $id): RedirectResponse
    {
        $requestModel = \App\Models\Request::findOrFail($id);
        
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // TODO: Create report record
        
        return redirect()->route('public.requests.show', ['id' => $requestModel->id, 'slug' => $requestModel->slug])
            ->with('success', 'تم الإبلاغ عن الطلب. شكراً لك.');
    }
}

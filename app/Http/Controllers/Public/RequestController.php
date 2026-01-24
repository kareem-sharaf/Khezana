<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequestsRequest;
use App\Models\Setting;
use App\Read\Requests\Models\RequestReadModel;
use App\Read\Requests\Queries\BrowseRequestsQuery;
use App\Read\Requests\Queries\ViewRequestQuery;
use App\Services\Cache\CacheService;
use App\Services\Cache\CategoryCacheService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function __construct(
        private readonly BrowseRequestsQuery $browseRequestsQuery,
        private readonly ViewRequestQuery $viewRequestQuery,
        private readonly CacheService $cacheService,
        private readonly CategoryCacheService $categoryCacheService,
    ) {
    }

    public function createInfo(): View
    {
        return view('public.requests.create-info');
    }

    public function index(FilterRequestsRequest $request): View
    {
        $sort = $request->validated()['sort'] ?? 'created_at_desc';
        $page = max(1, (int) ($request->validated()['page'] ?? 1));
        $perPage = min(50, max(1, (int) ($request->validated()['per_page'] ?? 9)));
        $locale = app()->getLocale();

        // Build filters array
        $filters = array_filter([
            'category_id' => $request->validated()['category_id'] ?? null,
            'search' => $request->validated()['search'] ?? null,
        ], fn($value) => $value !== null && $value !== '');

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

        // Ensure pagination preserves all query parameters (filters, sort, etc.)
        $requests->appends($request->query());

        // Get categories for filter dropdown
        $categories = $this->categoryCacheService->getTree();

        // Calculate active filters count for badge
        $activeFiltersCount = count($filters);

        return view('public.requests.index', [
            'requests' => $requests,
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

        $categories = $this->categoryCacheService->getTree();
        $feePercent = (float) Setting::deliveryServiceFeePercent();
        $preCreationRules = [
            ['icon' => '💰', 'text' => __('requests.detail.offer_form.pre_creation_notice.rule_fee', ['percent' => (string) (int) $feePercent])],
            ['icon' => '📞', 'text' => __('requests.detail.offer_form.pre_creation_notice.rule_contact')],
        ];

        return view('public.requests.show', [
            'request' => $requestModel,
            'categories' => $categories,
            'feePercent' => $feePercent,
            'preCreationRules' => $preCreationRules,
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

        $createOfferAction = app(\App\Actions\Offer\CreateOfferAction::class);

        try {
            $createOfferAction->execute(
                $validated,
                $requestModel,
                $request->user()
            );
        } catch (\Exception $e) {
            return redirect()->route('public.requests.show', ['id' => $requestModel->id, 'slug' => $requestModel->slug])
                ->with('error', $e->getMessage());
        }

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

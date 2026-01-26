<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Actions\Item\CreateItemAction;
use App\Actions\Offer\CreateOfferAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRequestsRequest;
use App\Models\Item;
use App\Models\Setting;
use App\Read\Items\Models\ItemReadModel;
use App\Read\Requests\Models\RequestReadModel;
use App\Read\Requests\Queries\BrowseRequestsQuery;
use App\Read\Requests\Queries\ViewRequestQuery;
use App\Services\Cache\CacheService;
use App\Services\Cache\CategoryCacheService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // Fetch suggested items from the same category
        $suggestedItems = collect();
        $categoryAttributes = collect();
        if ($requestModel->category) {
            $suggestedItems = Item::query()
                ->publishedAndAvailable()
                ->where('category_id', $requestModel->category->id)
                ->when($requestModel->user, fn($q) => $q->where('user_id', '!=', $requestModel->user->id))
                ->with(['images', 'category'])
                ->latest()
                ->limit(6)
                ->get()
                ->map(fn($item) => ItemReadModel::fromModel($item));

            // Fetch category attributes for the offer form
            $category = \App\Models\Category::with(['attributes.values', 'parent.attributes.values'])->find($requestModel->category->id);
            if ($category) {
                $categoryAttributes = $category->getAllAttributes()->map(fn($attr) => [
                    'id' => $attr->id,
                    'name' => $attr->name,
                    'slug' => $attr->slug,
                    'type' => $attr->type->value,
                    'is_required' => $attr->is_required,
                    'values' => $attr->values->pluck('value')->toArray(),
                ]);
            }
        }

        return view('public.requests.show', [
            'request' => $requestModel,
            'categories' => $categories,
            'feePercent' => $feePercent,
            'preCreationRules' => $preCreationRules,
            'suggestedItems' => $suggestedItems,
            'categoryAttributes' => $categoryAttributes,
        ]);
    }

    public function submitOffer(Request $request, int $id): RedirectResponse
    {
        $requestModel = \App\Models\Request::findOrFail($id);
        $user = $request->user();

        // Validate all data including item fields
        $validated = $request->validate([
            // Item fields
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'condition' => 'required|in:new,used',
            'operation_type' => 'required|in:sell,rent,donate',
            'price' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,jpg,png|max:5120',
            'attributes' => 'nullable|array',
            'attributes.*' => 'nullable|string|max:255',
            // Offer fields
            'message' => 'nullable|string|max:1000',
        ]);

        // Ensure category_id matches the request's category
        if ((int) $validated['category_id'] !== $requestModel->category_id) {
            return redirect()->route('public.requests.show', ['id' => $requestModel->id, 'slug' => $requestModel->slug])
                ->with('error', __('requests.messages.category_mismatch'));
        }

        $createItemAction = app(CreateItemAction::class);
        $createOfferAction = app(CreateOfferAction::class);

        try {
            // Prepare item data
            $itemData = [
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'condition' => $validated['condition'],
                'operation_type' => $validated['operation_type'],
                'price' => $validated['price'] ?? null,
                'deposit_amount' => $validated['deposit_amount'] ?? null,
            ];

            // Get attributes if provided
            $attributes = $validated['attributes'] ?? null;
            
            // Get images if provided
            $images = $request->hasFile('images') ? $request->file('images') : null;

            // Create the item (this will also submit for approval)
            $item = $createItemAction->execute($itemData, $user, $attributes, $images);

            // Create the offer linked to the item
            $offerData = [
                'operation_type' => $validated['operation_type'],
                'price' => $validated['price'] ?? null,
                'deposit_amount' => $validated['deposit_amount'] ?? null,
                'message' => $validated['message'] ?? null,
                'item_id' => $item->id,
            ];

            $createOfferAction->execute($offerData, $requestModel, $user);

            return redirect()->route('public.requests.show', ['id' => $requestModel->id, 'slug' => $requestModel->slug])
                ->with('success', __('requests.messages.offer_submitted'));

        } catch (\Exception $e) {
            Log::error('SubmitOffer failed', [
                'request_id' => $id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->route('public.requests.show', ['id' => $requestModel->id, 'slug' => $requestModel->slug])
                ->with('error', __('requests.messages.offer_failed') . ': ' . $e->getMessage());
        }
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

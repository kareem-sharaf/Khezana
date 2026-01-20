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

    public function index(Request $request): View
    {
        $filters = [
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
}

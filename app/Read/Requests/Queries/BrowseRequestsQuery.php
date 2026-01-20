<?php

declare(strict_types=1);

namespace App\Read\Requests\Queries;

use App\Enums\ApprovalStatus;
use App\Enums\OfferStatus;
use App\Models\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BrowseRequestsQuery
{
    public function execute(array $filters = [], ?string $sort = null, int $page = 1, int $perPage = 20): LengthAwarePaginator
    {
        $query = Request::query()
            ->whereHas('approvalRelation', fn($q) => $q->where('status', ApprovalStatus::APPROVED->value))
            ->whereNull('deleted_at')
            ->whereNull('archived_at');

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category_id']) && $filters['category_id']) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        match($sort) {
            'status_asc' => $query->orderBy('status', 'asc'),
            'status_desc' => $query->orderBy('status', 'desc'),
            'title_asc' => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            'updated_at_desc' => $query->orderBy('updated_at', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $query->select('id', 'title', 'slug', 'description', 'status', 'user_id', 'category_id', 'created_at', 'updated_at');

        $query->with([
            'user:id,name',
            'category:id,name,slug',
            'approvalRelation:id,approvable_type,approvable_id,status',
        ]);

        $query->withCount([
            'offers' => fn($q) => $q->where('status', OfferStatus::PENDING->value)
        ]);

        return $query->paginate(min($perPage, 50), ['*'], 'page', max(1, $page));
    }
}

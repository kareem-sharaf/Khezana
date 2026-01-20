<?php

declare(strict_types=1);

namespace App\Read\Items\Queries;

use App\Enums\ApprovalStatus;
use App\Enums\ItemAvailability;
use App\Models\Item;
use App\Models\User;
use App\Read\Items\Models\ItemReadModel;

class ViewItemQuery
{
    public function execute(int $itemId, ?string $slug = null, ?User $user = null): ?ItemReadModel
    {
        $query = Item::query()
            ->where('id', $itemId)
            ->where(function($q) use ($user) {
                $q->whereHas('approvalRelation', fn($a) => $a->where('status', ApprovalStatus::APPROVED->value))
                  ->where(function($av) {
                      $av->where('availability_status', ItemAvailability::AVAILABLE->value)
                         ->orWhere('is_available', true);
                  })
                  ->whereNull('deleted_at')
                  ->whereNull('archived_at');

                if ($user) {
                    $q->orWhere('user_id', $user->id);
                }
            });

        if ($slug) {
            $query->where('slug', $slug);
        }

        $item = $query->select('id', 'title', 'slug', 'description', 'price', 'deposit_amount', 'operation_type', 'availability_status', 'user_id', 'category_id', 'created_at', 'updated_at')
            ->with([
                'user:id,name,created_at',
                'category:id,name,slug,description',
                'images' => fn($q) => $q->select('id', 'item_id', 'path', 'is_primary', 'alt')
                                       ->orderBy('is_primary', 'desc'),
                'itemAttributes.attribute:id,name,type',
                'approvalRelation:id,approvable_type,approvable_id,status,reviewed_at',
            ])
            ->first();

        if (!$item) {
            return null;
        }

        return ItemReadModel::fromModel($item);
    }
}

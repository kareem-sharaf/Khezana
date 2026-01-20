<?php

declare(strict_types=1);

namespace App\Read\Requests\Queries;

use App\Enums\ApprovalStatus;
use App\Models\Request;
use App\Models\User;
use App\Read\Requests\Models\RequestReadModel;

class ViewRequestQuery
{
    public function execute(int $requestId, ?string $slug = null, ?User $user = null): ?RequestReadModel
    {
        $query = Request::query()
            ->where('id', $requestId)
            ->where(function($q) use ($user) {
                $q->whereHas('approvalRelation', fn($a) => $a->where('status', ApprovalStatus::APPROVED->value))
                  ->whereNull('deleted_at')
                  ->whereNull('archived_at');

                if ($user) {
                    $q->orWhere('user_id', $user->id);
                }
            });

        if ($slug) {
            $query->where('slug', $slug);
        }

        $request = $query->select('id', 'title', 'slug', 'description', 'status', 'user_id', 'category_id', 'created_at', 'updated_at')
            ->with([
                'user:id,name,created_at',
                'category:id,name,slug,description',
                'itemAttributes.attribute:id,name,type',
                'approvalRelation:id,approvable_type,approvable_id,status,reviewed_at',
                'offers' => function($q) use ($user) {
                    if ($user) {
                        $q->where(function($o) use ($user) {
                            $o->whereHas('request', fn($r) => $r->where('user_id', $user->id))
                              ->orWhere('user_id', $user->id);
                        });
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                    $q->with([
                        'user:id,name',
                        'item:id,title,price,availability_status' => [
                            'images' => fn($img) => $img->where('is_primary', true)->select('id', 'item_id', 'path')
                        ],
                        'request:id,title,status',
                    ]);
                },
            ])
            ->first();

        if (!$request) {
            return null;
        }

        return RequestReadModel::fromModel($request);
    }
}

<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\Approval\ContentApproved;
use App\Events\Approval\ContentArchived;
use App\Events\Approval\ContentRejected;
use App\Models\Item;
use App\Services\Cache\CacheService;
use Illuminate\Support\Facades\Log;

class InvalidateItemCache
{
    public function __construct(
        private readonly CacheService $cacheService
    ) {
    }

    public function handle(ContentApproved|ContentRejected|ContentArchived $event): void
    {
        $approvable = $event->approval->approvable;
        
        if (!$approvable instanceof Item) {
            return;
        }

        $this->cacheService->invalidateItem($approvable->id);
        
        Log::info('Item cache invalidated', [
            'item_id' => $approvable->id,
            'event' => class_basename($event),
        ]);
    }
}

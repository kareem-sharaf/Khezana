<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\Approval\ContentApproved;
use App\Events\Approval\ContentArchived;
use App\Events\Approval\ContentRejected;
use App\Models\Request;
use App\Services\Cache\CacheService;
use Illuminate\Support\Facades\Log;

class InvalidateRequestCache
{
    public function __construct(
        private readonly CacheService $cacheService
    ) {
    }

    public function handle(ContentApproved|ContentRejected|ContentArchived $event): void
    {
        $approvable = $event->approval->approvable;
        
        if (!$approvable instanceof Request) {
            return;
        }

        $this->cacheService->invalidateRequest($approvable->id);
        
        Log::info('Request cache invalidated', [
            'request_id' => $approvable->id,
            'event' => class_basename($event),
        ]);
    }
}

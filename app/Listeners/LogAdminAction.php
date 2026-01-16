<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\Services\AdminActionLogService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogAdminAction implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(
        private AdminActionLogService $logService
    ) {
    }

    /**
     * Handle UserCreated event.
     */
    public function handleUserCreated(UserCreated $event): void
    {
        $adminId = Auth::id() ?? $event->user->id;

        $this->logService->logCreate(
            model: $event->user,
            adminId: $adminId,
            notes: 'User created'
        );
    }

    /**
     * Handle UserUpdated event.
     */
    public function handleUserUpdated(UserUpdated $event): void
    {
        if (!Auth::check()) {
            return;
        }

        $this->logService->logUpdate(
            model: $event->user,
            adminId: Auth::id(),
            oldValues: $event->user->getOriginal(),
            notes: 'User updated'
        );
    }

    /**
     * Handle UserDeleted event.
     */
    public function handleUserDeleted(UserDeleted $event): void
    {
        if (!Auth::check()) {
            return;
        }

        $this->logService->logDelete(
            model: $event->user,
            adminId: Auth::id(),
            notes: 'User deleted'
        );
    }
}

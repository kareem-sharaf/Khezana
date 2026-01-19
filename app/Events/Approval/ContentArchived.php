<?php

declare(strict_types=1);

namespace App\Events\Approval;

use App\Models\Approval;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when content is archived
 */
class ContentArchived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Approval $approval
    ) {
    }
}

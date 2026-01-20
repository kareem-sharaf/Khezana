<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApprovalResource\Pages;

use App\Filament\Resources\ApprovalResource;
use Filament\Resources\Pages\ListRecords;

/**
 * List Approvals Page
 * 
 * Displays all pending approvals by default
 */
class ListApprovals extends ListRecords
{
    protected static string $resource = ApprovalResource::class;

    protected ?string $pollingInterval = '30s';

    protected function getHeaderActions(): array
    {
        return [
            // No create action - approvals are created when content is submitted
        ];
    }
}

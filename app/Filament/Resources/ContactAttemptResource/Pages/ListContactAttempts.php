<?php

namespace App\Filament\Resources\ContactAttemptResource\Pages;

use App\Filament\Resources\ContactAttemptResource;
use App\Filament\Resources\ContactAttemptResource\Widgets\ContactAttemptStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactAttempts extends ListRecords
{
    protected static string $resource = ContactAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - read only resource
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ContactAttemptStats::class,
        ];
    }
}

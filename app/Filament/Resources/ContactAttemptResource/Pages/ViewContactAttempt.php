<?php

namespace App\Filament\Resources\ContactAttemptResource\Pages;

use App\Filament\Resources\ContactAttemptResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContactAttempt extends ViewRecord
{
    protected static string $resource = ContactAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No actions - read only
        ];
    }
}

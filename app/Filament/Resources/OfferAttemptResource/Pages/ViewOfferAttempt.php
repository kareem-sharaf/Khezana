<?php

namespace App\Filament\Resources\OfferAttemptResource\Pages;

use App\Filament\Resources\OfferAttemptResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOfferAttempt extends ViewRecord
{
    protected static string $resource = OfferAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No actions - read only
        ];
    }
}

<?php

namespace App\Filament\Resources\OfferAttemptResource\Pages;

use App\Filament\Resources\OfferAttemptResource;
use App\Filament\Resources\OfferAttemptResource\Widgets\OfferAttemptStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOfferAttempts extends ListRecords
{
    protected static string $resource = OfferAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - read only resource
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OfferAttemptStats::class,
        ];
    }
}

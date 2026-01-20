<?php

declare(strict_types=1);

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRequests extends ListRecords
{
    protected static string $resource = RequestResource::class;

    protected ?string $pollingInterval = '30s';

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}

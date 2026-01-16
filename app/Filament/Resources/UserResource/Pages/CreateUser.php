<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\DTOs\UserDTO;
use App\DTOs\UserProfileDTO;
use App\Filament\Resources\UserResource;
use App\Services\AdminActionLogService;
use App\Services\UserService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove password_confirmation as it's not a database field
        unset($data['password_confirmation']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Log admin action
        if (Auth::check()) {
            app(AdminActionLogService::class)->logCreate(
                model: $this->record,
                adminId: Auth::id(),
                notes: 'User created via Filament admin panel'
            );
        }
    }
}

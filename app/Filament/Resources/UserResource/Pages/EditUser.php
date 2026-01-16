<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Services\AdminActionLogService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->before(function () {
                    if ($this->record->isSuperAdmin()) {
                        throw new \Exception('Cannot delete super admin user');
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove password if not provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Remove password_confirmation
        unset($data['password_confirmation']);

        return $data;
    }

    protected function afterSave(): void
    {
        // Log admin action
        if (Auth::check()) {
            app(AdminActionLogService::class)->logUpdate(
                model: $this->record,
                adminId: Auth::id(),
                oldValues: $this->record->getOriginal(),
                notes: 'User updated via Filament admin panel'
            );
        }
    }
}

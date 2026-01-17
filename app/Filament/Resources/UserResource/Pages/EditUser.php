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

    protected array $rolesToSync = [];
    protected array $oldValues = [];

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
        // Store old values before save for logging
        $this->oldValues = $this->record->getOriginal();

        // Remove password if not provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Remove password_confirmation
        unset($data['password_confirmation']);

        // Extract roles from data (will be handled in afterSave)
        $this->rolesToSync = $data['roles'] ?? [];
        unset($data['roles']);

        return $data;
    }

    protected function afterSave(): void
    {
        // Sync roles if provided and user has permission
        if (!empty($this->rolesToSync) && Auth::user()?->can('manageRoles', $this->record)) {
            // Prevent removing super_admin role from super_admin users
            if ($this->record->isSuperAdmin() && !Auth::user()?->isSuperAdmin()) {
                // Keep super_admin role
                $this->rolesToSync[] = 'super_admin';
                $this->rolesToSync = array_unique($this->rolesToSync);
            }
            $this->record->syncRoles($this->rolesToSync);
        }

        // Log admin action
        if (Auth::check()) {
            app(AdminActionLogService::class)->logUpdate(
                model: $this->record,
                adminId: Auth::id(),
                oldValues: $this->oldValues,
                notes: 'User updated via Filament admin panel'
            );
        }
    }
}

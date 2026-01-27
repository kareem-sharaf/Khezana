<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\DTOs\UserDTO;
use App\DTOs\UserProfileDTO;
use App\Filament\Resources\UserResource;
use App\Services\AdminActionLogService;
use App\Services\UserService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected array $rolesToAssign = [];

    /**
     * Validate branch_id based on roles before creating user.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract roles
        $this->rolesToAssign = $data['roles'] ?? [];

        // Validate branch_id based on roles
        $this->validateBranchForRoles(
            $this->rolesToAssign,
            $data['branch_id'] ?? null
        );

        // Remove password_confirmation as it's not a database field
        unset($data['password_confirmation']);
        unset($data['roles']);

        return $data;
    }

    /**
     * Validate that branch_id matches the role requirements.
     *
     * @throws ValidationException
     */
    private function validateBranchForRoles(array $roles, ?int $branchId): void
    {
        $isSeller = in_array('seller', $roles);
        $isAdmin = in_array('admin', $roles) || in_array('super_admin', $roles);
        $isUser = in_array('user', $roles);

        // Seller must have branch_id
        if ($isSeller && empty($branchId)) {
            throw ValidationException::withMessages([
                'branch_id' => ['البائع يجب أن يكون مرتبطاً بفرع'],
            ]);
        }

        // Admin/Super Admin/User cannot have branch_id
        if (($isAdmin || $isUser) && !empty($branchId)) {
            $roleLabel = $isAdmin ? 'إداري' : 'عادي';
            throw ValidationException::withMessages([
                'branch_id' => ["لا يمكن إسناد فرع لمستخدم ذو دور $roleLabel"],
            ]);
        }
    }

    protected function afterCreate(): void
    {
        // Assign roles if provided
        if (!empty($this->rolesToAssign)) {
            $this->record->syncRoles($this->rolesToAssign);
        } else {
            // Assign default 'user' role if no roles provided
            $this->record->assignRole('user');
        }

        // Log admin action
        if (Auth::check()) {
            app(AdminActionLogService::class)->logCreate(
                model: $this->record,
                adminId: Auth::id(),
                notes: sprintf(
                    'User created via Filament admin panel with roles: %s',
                    implode(', ', $this->rolesToAssign)
                )
            );
        }

        Notification::make()
            ->success()
            ->title('تم إنشاء المستخدم بنجاح')
            ->body(sprintf(
                'تم إنشاء المستخدم %s برقم هاتف %s',
                $this->record->name,
                $this->record->phone
            ))
            ->send();
    }
}

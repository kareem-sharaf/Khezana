<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('User Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->required(),
                        TextInput::make('password')
                            ->password()
                            ->required(fn($livewire) => $livewire instanceof Pages\CreateUser)
                            ->minLength(8)
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->visible(fn($livewire) => $livewire instanceof Pages\CreateUser),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->required(fn($livewire) => $livewire instanceof Pages\CreateUser)
                            ->same('password')
                            ->visible(fn($livewire) => $livewire instanceof Pages\CreateUser),
                    ])
                    ->columns(2),

                Section::make('Roles & Permissions')
                    ->schema([
                        Select::make('roles')
                            ->label('Roles')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->options(Role::pluck('name', 'name'))
                            ->searchable()
                            ->required(false),
                    ])
                    ->visible(fn() => auth()->user()?->can('manageRoles', User::class)),

                Section::make('Profile Information')
                    ->schema([
                        TextInput::make('profile.city')
                            ->maxLength(255),
                        Textarea::make('profile.address')
                            ->rows(3),
                        TextInput::make('profile.avatar')
                            ->maxLength(255),
                    ])
                    ->relationship('profile')
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'danger',
                        'inactive' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),
                Tables\Filters\Filter::make('roles')
                    ->form([
                        Forms\Components\Select::make('role')
                            ->options(Role::pluck('name', 'name'))
                            ->searchable(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['role'],
                                fn(Builder $query, $role): Builder => $query->whereHas('roles', fn($q) => $q->where('name', $role)),
                            );
                    }),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->before(function (User $record) {
                        if ($record->isSuperAdmin()) {
                            throw new \Exception('Cannot delete super admin user');
                        }
                    }),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->isSuperAdmin()) {
                                    throw new \Exception('Cannot delete super admin user');
                                }
                            }
                        }),
                ]),
            ])
            ->emptyStateActions([
                Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['roles', 'profile']);
    }

    /**
     * Check if user can view any models.
     * Uses UserPolicy::viewAny() automatically.
     */
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('viewAny', User::class) ?? false;
    }

    /**
     * Check if user can create models.
     * Uses UserPolicy::create() automatically.
     */
    public static function canCreate(): bool
    {
        return auth()->user()?->can('create', User::class) ?? false;
    }
}

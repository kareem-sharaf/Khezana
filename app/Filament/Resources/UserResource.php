<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components\Section;
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
        return __('filament.navigation_groups.user_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-dashboard.Users');
    }

    public static function getModelLabel(): string
    {
        return __('filament-dashboard.User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-dashboard.Users');
    }

    public static function getNavigationBadge(): ?string
    {
        $suspendedCount = static::getModel()::where('status', 'suspended')->count();
        return $suspendedCount > 0 ? (string) $suspendedCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $suspendedCount = static::getModel()::where('status', 'suspended')->count();
        return $suspendedCount > 0 ? 'danger' : null;
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('filament-dashboard.User Information'))
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
                                'active' => __('filament-dashboard.Active'),
                                'inactive' => __('filament-dashboard.Inactive'),
                                'suspended' => __('filament-dashboard.Suspended'),
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

                Section::make(__('filament-dashboard.Roles & Permissions'))
                    ->schema([
                        Select::make('roles')
                            ->label(__('filament-dashboard.Roles'))
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->searchable()
                            ->preload()
                            ->required(false),
                    ])
                    ->visible(fn() => auth()->user()?->can('manageRoles', User::class)),

                Section::make(__('filament-dashboard.Profile Information'))
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
                    ->label(__('filament-dashboard.ID'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament-dashboard.Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament-dashboard.Email'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('filament-dashboard.Phone'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament-dashboard.Status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'danger',
                        'inactive' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('filament-dashboard.Roles'))
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
                        'active' => __('filament-dashboard.Active'),
                        'inactive' => __('filament-dashboard.Inactive'),
                        'suspended' => __('filament-dashboard.Suspended'),
                    ]),
                Tables\Filters\Filter::make('roles')
                    ->form([
                        Forms\Components\Select::make('role')
                            ->relationship('roles', 'name', fn($query) => $query->orderBy('name'))
                            ->searchable()
                            ->preload(),
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

    // canViewAny() and canCreate() removed - Filament automatically uses UserPolicy
}

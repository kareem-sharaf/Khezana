<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\User;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-shield-check';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation_groups.user_management');
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-dashboard.Roles');
    }

    public static function getModelLabel(): string
    {
        return __('filament-dashboard.Role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-dashboard.Roles');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('filament-dashboard.Role Information'))
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label(__('filament-dashboard.Role Name'))
                            ->placeholder('e.g., admin, manager'),
                    ])
                    ->columns(1),

                Section::make(__('filament-dashboard.Permissions'))
                    ->schema([
                        Select::make('permissions')
                            ->label(__('filament-dashboard.Permissions'))
                            ->multiple()
                            ->relationship('permissions', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText(__('filament-dashboard.Select permissions for this role'))
                            ->required(false),
                    ])
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
                    ->label(__('filament-dashboard.Role Name'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('permissions.name')
                    ->label(__('filament-dashboard.Permissions'))
                    ->badge()
                    ->color('success')
                    ->separator(',')
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label(__('filament-dashboard.Guard'))
                    ->badge()
                    ->color('gray')
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
                Tables\Filters\SelectFilter::make('guard_name')
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->before(function (Role $record) {
                        // Prevent deletion of super_admin role
                        if ($record->name === 'super_admin') {
                            throw new \Exception('Cannot delete super_admin role');
                        }
                    }),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->name === 'super_admin') {
                                    throw new \Exception('Cannot delete super_admin role');
                                }
                            }
                        }),
                ]),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        /** @var User $user */
        $user = Auth::user();
        // @phpstan-ignore-next-line
        return $user->hasPermissionTo('manage_roles');
    }
}

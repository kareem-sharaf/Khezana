<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-key';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation_groups.user_management');
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-dashboard.Permissions');
    }

    public static function getModelLabel(): string
    {
        return __('filament-dashboard.Permission');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-dashboard.Permissions');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('filament-dashboard.Permission Information'))
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label(__('filament-dashboard.Permission Name'))
                            ->placeholder('e.g., manage_users, view_products')
                            ->helperText(__('filament-dashboard.Use snake_case format (e.g., manage_users)')),
                    ])
                    ->columns(1),

                Section::make(__('filament-dashboard.Roles'))
                    ->schema([
                        Select::make('roles')
                            ->label(__('filament-dashboard.Roles'))
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText(__('filament-dashboard.Select roles that have this permission'))
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
                    ->label(__('filament-dashboard.Permission Name'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('filament-dashboard.Roles'))
                    ->badge()
                    ->color('primary')
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
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'view' => Pages\ViewPermission::route('/{record}'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()?->can('manage_permissions');
    }
}

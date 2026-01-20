<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\RequestResource\Pages;
use App\Enums\RequestStatus;
use App\Models\Request;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Request Resource for Filament Admin Panel
 */
class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-chat-bubble-left-right';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-dashboard.Content Management');
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-dashboard.Requests');
    }

    public static function getModelLabel(): string
    {
        return __('filament-dashboard.Request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-dashboard.Requests');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('filament-dashboard.Request Information'))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('filament-dashboard.Title'))
                            ->required()
                            ->maxLength(255)
                            ->disabled(),
                        Textarea::make('description')
                            ->label(__('filament-dashboard.Description'))
                            ->rows(3)
                            ->disabled(),
                        Select::make('user_id')
                            ->label(__('filament-dashboard.Owner'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        Select::make('category_id')
                            ->label(__('filament-dashboard.Category'))
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        Select::make('status')
                            ->label(__('filament-dashboard.Status'))
                            ->options(RequestStatus::options())
                            ->required()
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('filament-dashboard.ID'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament-dashboard.Title'))
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament-dashboard.Owner'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('filament-dashboard.Category'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament-dashboard.Status'))
                    ->badge()
                    ->color(fn(RequestStatus $state): string => $state->color())
                    ->formatStateUsing(fn(RequestStatus $state): string => $state->label())
                    ->sortable(),
                Tables\Columns\TextColumn::make('approvalRelation.status')
                    ->label(__('filament-dashboard.Approval Status'))
                    ->badge()
                    ->color(fn($state) => $state?->color())
                    ->formatStateUsing(fn($state) => $state?->label())
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament-dashboard.Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament-dashboard.Status'))
                    ->options(RequestStatus::options()),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label(__('filament-dashboard.Category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('approvalRelation.status')
                    ->label(__('filament-dashboard.Approval Status'))
                    ->relationship('approvalRelation', 'status')
                    ->options([
                        'pending' => __('filament-dashboard.Pending'),
                        'approved' => __('filament-dashboard.Approved'),
                        'rejected' => __('filament-dashboard.Rejected'),
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make(),
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
            'index' => Pages\ListRequests::route('/'),
            'view' => Pages\ViewRequest::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'category', 'approvalRelation']);
    }

    public static function canCreate(): bool
    {
        return false; // Admins cannot create requests, only users can
    }

    public static function canEdit(Model $record): bool
    {
        return false; // Admins cannot edit request content, only approve/reject
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\OfferResource\Pages;
use App\Enums\OfferStatus;
use App\Enums\OperationType;
use App\Models\Offer;
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
 * Offer Resource for Filament Admin Panel
 */
class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-hand-raised';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-dashboard.Content Management');
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-dashboard.Offers');
    }

    public static function getModelLabel(): string
    {
        return __('filament-dashboard.Offer');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-dashboard.Offers');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('filament-dashboard.Offer Information'))
                    ->schema([
                        Select::make('request_id')
                            ->label(__('filament-dashboard.Request'))
                            ->relationship('request', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        Select::make('user_id')
                            ->label(__('filament-dashboard.Offer Owner'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        Select::make('item_id')
                            ->label(__('filament-dashboard.Item'))
                            ->relationship('item', 'title')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        Select::make('operation_type')
                            ->label(__('filament-dashboard.Operation Type'))
                            ->options(OperationType::getOptions())
                            ->required()
                            ->disabled(),
                        TextInput::make('price')
                            ->label(__('filament-dashboard.Price'))
                            ->numeric()
                            ->disabled(),
                        TextInput::make('deposit_amount')
                            ->label(__('filament-dashboard.Deposit Amount'))
                            ->numeric()
                            ->disabled(),
                        Select::make('status')
                            ->label(__('filament-dashboard.Status'))
                            ->options(OfferStatus::options())
                            ->required()
                            ->disabled(),
                        Textarea::make('message')
                            ->label(__('filament-dashboard.Message'))
                            ->rows(3)
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
                Tables\Columns\TextColumn::make('request.title')
                    ->label(__('filament-dashboard.Request'))
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament-dashboard.Offer Owner'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('operation_type')
                    ->label(__('filament-dashboard.Operation Type'))
                    ->badge()
                    ->color(fn (OperationType $state): string => $state->getColor())
                    ->formatStateUsing(fn (OperationType $state): string => $state->getLabel())
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('filament-dashboard.Price'))
                    ->money('SYP')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament-dashboard.Status'))
                    ->badge()
                    ->color(fn (OfferStatus $state): string => $state->color())
                    ->formatStateUsing(fn (OfferStatus $state): string => $state->label())
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
                    ->options(OfferStatus::options()),
                Tables\Filters\SelectFilter::make('operation_type')
                    ->label(__('filament-dashboard.Operation Type'))
                    ->options(OperationType::getOptions()),
                Tables\Filters\SelectFilter::make('request_id')
                    ->label(__('filament-dashboard.Request'))
                    ->relationship('request', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\Action::make('force_reject')
                    ->label(__('filament-dashboard.Force Reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Offer $record) {
                        $record->update(['status' => OfferStatus::REJECTED]);
                    })
                    ->visible(fn (Offer $record) => $record->isPending() && auth()->user()?->can('manage', $record)),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    //
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
            'index' => Pages\ListOffers::route('/'),
            'view' => Pages\ViewOffer::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['request', 'user', 'item']);
    }

    public static function canCreate(): bool
    {
        return false; // Admins cannot create offers, only users can
    }

    public static function canEdit(Model $record): bool
    {
        return false; // Admins cannot edit offers, only view and force reject
    }
}

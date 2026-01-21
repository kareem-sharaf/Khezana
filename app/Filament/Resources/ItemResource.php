<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Enums\ApprovalStatus;
use App\Enums\OperationType;
use App\Models\Item;
use Filament\Actions;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Item Resource for Filament Admin Panel
 */
class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-shopping-bag';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation_groups.moderation');
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getNavigationLabel(): string
    {
        return __('items.title');
    }

    public static function getModelLabel(): string
    {
        return __('items.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('items.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::pending()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::pending()->count();
        return $count > 0 ? 'warning' : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('filament-dashboard.Item Information'))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('filament-dashboard.Title'))
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label(__('filament-dashboard.Description'))
                            ->rows(3),
                        Select::make('user_id')
                            ->label(__('filament-dashboard.Owner'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled()
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name ?? ''),
                        Select::make('category_id')
                            ->label(__('filament-dashboard.Category'))
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
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
                        Toggle::make('is_available')
                            ->label(__('filament-dashboard.Is Available'))
                            ->default(true),
                    ])
                    ->columns(2),
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
                Tables\Columns\IconColumn::make('is_available')
                    ->label(__('filament-dashboard.Available'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approvalRelation.status')
                    ->label(__('filament-dashboard.Approval Status'))
                    ->badge()
                    ->color(fn ($state) => $state?->color())
                    ->formatStateUsing(fn ($state) => $state?->label())
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament-dashboard.Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('operation_type')
                    ->label(__('filament-dashboard.Operation Type'))
                    ->options(OperationType::getOptions()),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label(__('filament-dashboard.Category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_available')
                    ->label(__('filament-dashboard.Available'))
                    ->placeholder(__('filament-dashboard.All'))
                    ->trueLabel(__('filament-dashboard.Available Only'))
                    ->falseLabel(__('filament-dashboard.Unavailable Only')),
                Tables\Filters\SelectFilter::make('approvalRelation.status')
                    ->label(__('filament-dashboard.Approval Status'))
                    ->options([
                        ApprovalStatus::PENDING->value => ApprovalStatus::PENDING->label(),
                        ApprovalStatus::APPROVED->value => ApprovalStatus::APPROVED->label(),
                        ApprovalStatus::REJECTED->value => ApprovalStatus::REJECTED->label(),
                        ApprovalStatus::ARCHIVED->value => ApprovalStatus::ARCHIVED->label(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['value'])) {
                            return $query->whereHas('approvalRelation', function ($q) use ($data) {
                                $q->where('status', $data['value']);
                            });
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\DeleteAction::make()
                    ->label(__('filament-dashboard.Delete'))
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('reason')
                            ->label(__('items.deletion.reason_label'))
                            ->required()
                            ->rows(3)
                            ->placeholder(__('items.deletion.reason_placeholder'))
                            ->helperText(__('items.deletion.archive_hint')),
                        Checkbox::make('archive')
                            ->label(__('items.deletion.archive_option'))
                            ->helperText(__('items.deletion.archive_hint')),
                    ])
                    ->action(function (Item $record, array $data) {
                        $user = auth()->user();
                        $deleteAction = app(\App\Actions\Item\DeleteItemAction::class);
                        $deleteAction->softDelete($record, $user, $data['reason'] ?? null, $data['archive'] ?? false);

                        \Filament\Notifications\Notification::make()
                            ->title(__('items.messages.deleted'))
                            ->success()
                            ->send();
                    })
                    ->successNotificationTitle(__('items.messages.deleted'))
                    ->visible(fn (Item $record) => auth()->user()?->can('delete', $record)),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->label(__('filament-dashboard.Delete Selected'))
                        ->requiresConfirmation()
                        ->form([
                            Textarea::make('reason')
                                ->label(__('items.deletion.reason_label'))
                                ->required()
                                ->rows(3)
                                ->placeholder(__('items.deletion.reason_placeholder')),
                            Checkbox::make('archive')
                                ->label(__('items.deletion.archive_option'))
                                ->helperText(__('items.deletion.archive_hint')),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $user = \Illuminate\Support\Facades\Auth::user();
                            $deleteAction = app(\App\Actions\Item\DeleteItemAction::class);
                            $deletedCount = 0;

                            foreach ($records as $record) {
                                if ($user && $user->can('delete', $record)) {
                                    $deleteAction->softDelete($record, $user, $data['reason'] ?? null, $data['archive'] ?? false);
                                    $deletedCount++;
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title(__('items.messages.deleted') . ' (' . $deletedCount . ')')
                                ->success()
                                ->send();
                        })
                        ->successNotificationTitle(fn ($records) => __('items.messages.deleted') . ' (' . $records->count() . ')')
                        ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'super_admin'])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListItems::route('/'),
            'view' => Pages\ViewItem::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'category', 'images', 'approvalRelation']);
        // Note: Soft deleted items are hidden by default (SoftDeletes global scope)
        // Admins can see them by filtering or using a custom query if needed
    }

    // canViewAny removed - Panel-level access control handles admin/super_admin check
    // For resource-specific permissions, use Policies instead
}

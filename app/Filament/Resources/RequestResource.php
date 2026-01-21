<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\RequestResource\Pages;
use App\Enums\ApprovalStatus;
use App\Enums\RequestStatus;
use App\Models\Request;
use Filament\Actions;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Checkbox;
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
        return __('filament.navigation_groups.moderation');
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationLabel(): string
    {
        return __('requests.title');
    }

    public static function getModelLabel(): string
    {
        return __('requests.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('requests.plural');
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
                            ->label(__('requests.deletion.reason_label'))
                            ->required()
                            ->rows(3)
                            ->placeholder(__('requests.deletion.reason_placeholder'))
                            ->helperText(__('requests.deletion.archive_hint')),
                        Checkbox::make('archive')
                            ->label(__('requests.deletion.archive_option'))
                            ->helperText(__('requests.deletion.archive_hint')),
                    ])
                    ->action(function (Request $record, array $data) {
                        $user = \Illuminate\Support\Facades\Auth::user();
                        $deleteAction = app(\App\Actions\Request\DeleteRequestAction::class);
                        $deleteAction->softDelete($record, $user, $data['reason'] ?? null, $data['archive'] ?? false);

                        \Filament\Notifications\Notification::make()
                            ->title(__('requests.messages.deleted_successfully'))
                            ->success()
                            ->send();
                    })
                    ->successNotificationTitle(__('requests.messages.deleted_successfully'))
                    ->visible(fn (Request $record) => auth()->user()?->can('delete', $record)),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->label(__('filament-dashboard.Delete Selected'))
                        ->requiresConfirmation()
                        ->form([
                            Textarea::make('reason')
                                ->label(__('requests.deletion.reason_label'))
                                ->required()
                                ->rows(3)
                                ->placeholder(__('requests.deletion.reason_placeholder')),
                            Checkbox::make('archive')
                                ->label(__('requests.deletion.archive_option'))
                                ->helperText(__('requests.deletion.archive_hint')),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $user = \Illuminate\Support\Facades\Auth::user();
                            $deleteAction = app(\App\Actions\Request\DeleteRequestAction::class);
                            $deletedCount = 0;

                            foreach ($records as $record) {
                                if ($user && $user->can('delete', $record)) {
                                    $deleteAction->softDelete($record, $user, $data['reason'] ?? null, $data['archive'] ?? false);
                                    $deletedCount++;
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title(__('requests.messages.deleted_successfully') . ' (' . $deletedCount . ')')
                                ->success()
                                ->send();
                        })
                        ->successNotificationTitle(fn ($records) => __('requests.messages.deleted_successfully') . ' (' . $records->count() . ')')
                        ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'super_admin'])),
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
        // Note: Soft deleted requests are hidden by default (SoftDeletes global scope)
        // Admins can see them by filtering or using a custom query if needed
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

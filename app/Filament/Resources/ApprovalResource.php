<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Actions\Approval\ApproveAction;
use App\Actions\Approval\ArchiveAction;
use App\Actions\Approval\RejectAction;
use App\Enums\ApprovalStatus;
use App\Filament\Resources\ApprovalResource\Pages;
use App\Models\Approval;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Filament Resource for managing content approvals
 */
class ApprovalResource extends Resource
{
    protected static ?string $model = Approval::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-clipboard-document-check';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-dashboard.Content Management');
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-dashboard.Approvals');
    }

    public static function getModelLabel(): string
    {
        return __('filament-dashboard.Approval');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-dashboard.Approvals');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('filament-dashboard.Approval Information'))
                    ->schema([
                        Select::make('status')
                            ->label(__('filament-dashboard.Status'))
                            ->options([
                                ApprovalStatus::PENDING->value => ApprovalStatus::PENDING->label(),
                                ApprovalStatus::APPROVED->value => ApprovalStatus::APPROVED->label(),
                                ApprovalStatus::REJECTED->value => ApprovalStatus::REJECTED->label(),
                                ApprovalStatus::ARCHIVED->value => ApprovalStatus::ARCHIVED->label(),
                            ])
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                    ])
                    ->columns(1),

                Section::make(__('filament-dashboard.Content Information'))
                    ->schema([
                        Textarea::make('approvable_title')
                            ->label(__('filament-dashboard.Content Title'))
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($record) => $record?->approvable?->getApprovalTitle() ?? 'N/A'),
                    ])
                    ->columns(1)
                    ->visible(fn ($record) => $record !== null),

                Section::make(__('filament-dashboard.Rejection Reason'))
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label(__('filament-dashboard.Rejection Reason'))
                            ->rows(3)
                            ->placeholder(__('filament-dashboard.Enter the reason for rejection...'))
                            ->visible(fn ($record) => $record?->status === ApprovalStatus::REJECTED),
                    ])
                    ->columns(1)
                    ->visible(fn ($record) => $record !== null && $record->status === ApprovalStatus::REJECTED),
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

                Tables\Columns\TextColumn::make('approvable_type')
                    ->label(__('filament-dashboard.Content Type'))
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('approvable.title')
                    ->label(__('filament-dashboard.Content Title'))
                    ->limit(50)
                    ->formatStateUsing(fn ($record) => $record->approvable?->getApprovalTitle() ?? 'N/A')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament-dashboard.Status'))
                    ->badge()
                    ->color(fn (ApprovalStatus $state): string => $state->color())
                    ->formatStateUsing(fn (ApprovalStatus $state): string => $state->label())
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('submitter.name')
                    ->label(__('filament-dashboard.Submitted By'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label(__('filament-dashboard.Reviewed By'))
                    ->sortable()
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label(__('filament-dashboard.Reviewed At'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament-dashboard.Submitted At'))
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
                    ->label(__('filament-dashboard.Status'))
                    ->options([
                        ApprovalStatus::PENDING->value => ApprovalStatus::PENDING->label(),
                        ApprovalStatus::APPROVED->value => ApprovalStatus::APPROVED->label(),
                        ApprovalStatus::REJECTED->value => ApprovalStatus::REJECTED->label(),
                        ApprovalStatus::ARCHIVED->value => ApprovalStatus::ARCHIVED->label(),
                    ]),

                Tables\Filters\SelectFilter::make('approvable_type')
                    ->label(__('filament-dashboard.Content Type'))
                    ->options(function () {
                        // Get unique approvable types from database
                        return Approval::query()
                            ->distinct()
                            ->pluck('approvable_type')
                            ->mapWithKeys(fn ($type) => [$type => class_basename($type)])
                            ->toArray();
                    }),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('filament-dashboard.Submitted From')),
                        DatePicker::make('created_until')
                            ->label(__('filament-dashboard.Submitted Until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Actions\ViewAction::make(),
                Actions\Action::make('approve')
                    ->label(__('filament-dashboard.Approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Approval $record) {
                        app(ApproveAction::class)->execute($record, auth()->user());
                    })
                    ->visible(fn (Approval $record) => $record->isPending() && auth()->user()?->can('approve', $record)),

                Actions\Action::make('reject')
                    ->label(__('filament-dashboard.Reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label(__('filament-dashboard.Rejection Reason'))
                            ->required()
                            ->rows(3)
                            ->placeholder(__('filament-dashboard.Enter the reason for rejection...')),
                    ])
                    ->action(function (Approval $record, array $data) {
                        app(RejectAction::class)->execute($record, auth()->user(), $data['rejection_reason']);
                    })
                    ->visible(fn (Approval $record) => $record->isPending() && auth()->user()?->can('reject', $record)),

                Actions\Action::make('archive')
                    ->label(__('filament-dashboard.Archive'))
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (Approval $record) {
                        app(ArchiveAction::class)->execute($record, auth()->user());
                    })
                    ->visible(fn (Approval $record) => !$record->isArchived() && auth()->user()?->can('archive', $record)),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('super_admin')),
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
            'index' => Pages\ListApprovals::route('/'),
            'view' => Pages\ViewApproval::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['approvable', 'submitter', 'reviewer']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('viewAny', Approval::class) ?? false;
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources\ItemResource\Pages;

use App\Actions\Item\ApproveItemAction;
use App\Actions\Item\ArchiveItemAction;
use App\Actions\Item\RejectItemAction;
use App\Enums\ApprovalStatus;
use App\Filament\Resources\ItemResource;
use App\Models\Item;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewItem extends ViewRecord
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label(__('filament-dashboard.Approve'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (Item $record) {
                    app(ApproveItemAction::class)->execute($record, Auth::user());
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn (Item $record) => $record->isPending() && Auth::user()?->can('approve', $record->approval)),

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
                ->action(function (Item $record, array $data) {
                    app(RejectItemAction::class)->execute($record, Auth::user(), $data['rejection_reason']);
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn (Item $record) => $record->isPending() && Auth::user()?->can('reject', $record->approval)),

            Actions\Action::make('archive')
                ->label(__('filament-dashboard.Archive'))
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->form([
                    Textarea::make('reason')
                        ->label(__('filament-dashboard.Archive Reason'))
                        ->required()
                        ->rows(3)
                        ->placeholder(__('filament-dashboard.Enter the reason for archiving...')),
                ])
                ->action(function (Item $record, array $data) {
                    app(ArchiveItemAction::class)->execute($record, Auth::user(), $data['reason']);
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn (Item $record) => !$record->isArchived() && Auth::user()?->can('archive', $record->approval)),
        ];
    }
}

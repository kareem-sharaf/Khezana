<?php

declare(strict_types=1);

namespace App\Filament\Resources\ItemResource\Pages;

use App\Actions\Approval\ApproveAction;
use App\Actions\Approval\ArchiveAction;
use App\Actions\Approval\RejectAction;
use App\Enums\ApprovalStatus;
use App\Filament\Resources\ItemResource;
use App\Models\Item;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;

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
                    app(ApproveAction::class)->execute($record->approval, auth()->user());
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn (Item $record) => $record->isPending() && auth()->user()?->can('approve', $record->approval)),

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
                    app(RejectAction::class)->execute($record->approval, auth()->user(), $data['rejection_reason']);
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn (Item $record) => $record->isPending() && auth()->user()?->can('reject', $record->approval)),

            Actions\Action::make('archive')
                ->label(__('filament-dashboard.Archive'))
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function (Item $record) {
                    app(ArchiveAction::class)->execute($record->approval, auth()->user());
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn (Item $record) => !$record->isArchived() && auth()->user()?->can('archive', $record->approval)),
        ];
    }
}

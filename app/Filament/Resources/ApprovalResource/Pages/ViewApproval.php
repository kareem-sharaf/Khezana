<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApprovalResource\Pages;

use App\Actions\Approval\ApproveAction;
use App\Actions\Approval\ArchiveAction;
use App\Actions\Approval\RejectAction;
use App\Enums\ApprovalStatus;
use App\Filament\Resources\ApprovalResource;
use App\Models\Approval;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;

/**
 * View Approval Page
 */
class ViewApproval extends ViewRecord
{
    protected static string $resource = ApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label(__('filament-dashboard.Approve'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (Approval $record) {
                    app(ApproveAction::class)->execute($record, auth()->user());
                    $this->redirect($this->getResource()::getUrl('index'));
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
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn (Approval $record) => $record->isPending() && auth()->user()?->can('reject', $record)),

            Actions\Action::make('archive')
                ->label(__('filament-dashboard.Archive'))
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function (Approval $record) {
                    app(ArchiveAction::class)->execute($record, auth()->user());
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn (Approval $record) => !$record->isArchived() && auth()->user()?->can('archive', $record)),
        ];
    }
}

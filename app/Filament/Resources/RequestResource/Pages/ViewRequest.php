<?php

declare(strict_types=1);

namespace App\Filament\Resources\RequestResource\Pages;

use App\Actions\Approval\ApproveAction;
use App\Actions\Approval\ArchiveAction;
use App\Actions\Approval\RejectAction;
use App\Filament\Resources\RequestResource;
use App\Models\Request;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewRequest extends ViewRecord
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label(__('filament-dashboard.Approve'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (Request $record) {
                    $approval = $record->approval();
                    $user = Auth::user();

                    if (!$approval || !$user) {
                        return;
                    }

                    app(ApproveAction::class)->execute($approval, $user);
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn(Request $record) => $record->isPending() && Auth::user()?->can('approve', $record->approval())),

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
                ->action(function (Request $record, array $data) {
                    $approval = $record->approval();
                    $user = Auth::user();

                    if (!$approval || !$user) {
                        return;
                    }

                    app(RejectAction::class)->execute($approval, $user, $data['rejection_reason']);
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn(Request $record) => $record->isPending() && Auth::user()?->can('reject', $record->approval())),

            Actions\Action::make('archive')
                ->label(__('filament-dashboard.Archive'))
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function (Request $record) {
                    $approval = $record->approval();
                    $user = Auth::user();

                    if (!$approval || !$user) {
                        return;
                    }

                    app(ArchiveAction::class)->execute($approval, $user);
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn(Request $record) => $record->approval()?->status->isArchived() === false && Auth::user()?->can('archive', $record->approval())),
        ];
    }
}

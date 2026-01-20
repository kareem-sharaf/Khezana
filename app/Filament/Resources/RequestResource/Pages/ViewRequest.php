<?php

declare(strict_types=1);

namespace App\Filament\Resources\RequestResource\Pages;

use App\Actions\Request\ApproveRequestAction;
use App\Actions\Request\ArchiveRequestAction;
use App\Actions\Request\RejectRequestAction;
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
                    app(ApproveRequestAction::class)->execute($record, Auth::user());
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
                    app(RejectRequestAction::class)->execute($record, Auth::user(), $data['rejection_reason']);
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn(Request $record) => $record->isPending() && Auth::user()?->can('reject', $record->approval())),

            Actions\Action::make('archive')
                ->label(__('filament-dashboard.Archive'))
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function (Request $record) {
                    app(ArchiveRequestAction::class)->execute($record, Auth::user());
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn(Request $record) => $record->approval()?->status->isArchived() === false && Auth::user()?->can('archive', $record->approval())),
        ];
    }
}

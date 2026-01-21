<?php

declare(strict_types=1);

namespace App\Filament\Resources\RequestResource\Pages;

use App\Actions\Request\ApproveRequestAction;
use App\Actions\Request\ArchiveRequestAction;
use App\Actions\Request\DeleteRequestAction;
use App\Actions\Request\RejectRequestAction;
use App\Filament\Resources\RequestResource;
use App\Models\Request;
use Filament\Actions;
use Filament\Forms\Components\Checkbox;
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
                ->form([
                    Textarea::make('reason')
                        ->label(__('filament-dashboard.Archive Reason'))
                        ->required()
                        ->rows(3)
                        ->placeholder(__('filament-dashboard.Enter the reason for archiving...')),
                ])
                ->action(function (Request $record, array $data) {
                    app(ArchiveRequestAction::class)->execute($record, Auth::user(), $data['reason']);
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn(Request $record) => $record->approval()?->status->isArchived() === false && Auth::user()?->can('archive', $record->approval())),

            Actions\DeleteAction::make()
                ->label(__('filament-dashboard.Delete'))
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
                ->action(function (Request $record, array $data) {
                    $deleteAction = app(DeleteRequestAction::class);
                    $deleteAction->softDelete($record, Auth::user(), $data['reason'] ?? null, $data['archive'] ?? false);
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn (Request $record) => Auth::user()?->can('delete', $record)),

            Actions\Action::make('restore')
                ->label(__('filament-dashboard.Restore'))
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (Request $record) {
                    $record->restore();
                    if ($record->archived_at) {
                        $record->update(['archived_at' => null]);
                    }
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn (Request $record) => $record->trashed() && Auth::user()?->can('restore', $record)),
        ];
    }
}

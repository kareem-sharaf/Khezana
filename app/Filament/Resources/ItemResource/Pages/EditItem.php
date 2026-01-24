<?php

declare(strict_types=1);

namespace App\Filament\Resources\ItemResource\Pages;

use App\Actions\Item\UpdateItemAction;
use App\Filament\Resources\ItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditItem extends EditRecord
{
    protected static string $resource = ItemResource::class;

    protected bool $wasApproved = false;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->label(__('filament-dashboard.Delete'))
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label(__('items.deletion.reason_label'))
                        ->required()
                        ->rows(3)
                        ->placeholder(__('items.deletion.reason_placeholder')),
                    \Filament\Forms\Components\Checkbox::make('archive')
                        ->label(__('items.deletion.archive_option'))
                        ->helperText(__('items.deletion.archive_hint')),
                ])
                ->action(function (array $data) {
                    $deleteAction = app(\App\Actions\Item\DeleteItemAction::class);
                    $deleteAction->softDelete($this->record, Auth::user(), $data['reason'] ?? null, $data['archive'] ?? false);
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn () => Auth::user()?->can('delete', $this->record)),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store approval status before save
        $this->wasApproved = $this->record->isApproved();
        
        // Ensure user_id is not changed
        $data['user_id'] = $this->record->user_id;
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Invalidate cache after update
        $cacheService = app(\App\Services\Cache\CacheService::class);
        $cacheService->invalidateItem($this->record->id);

        // Re-submit for approval if item was approved and sensitive fields changed
        if ($this->wasApproved) {
            $hasSensitiveChanges = false;
            $sensitiveFields = ['price', 'operation_type', 'category_id'];
            
            foreach ($sensitiveFields as $field) {
                if ($this->record->wasChanged($field)) {
                    $hasSensitiveChanges = true;
                    break;
                }
            }

            if ($hasSensitiveChanges) {
                $approval = $this->record->approval();
                if ($approval && $approval->status !== \App\Enums\ApprovalStatus::PENDING) {
                    $submitAction = app(\App\Actions\Approval\SubmitForApprovalAction::class);
                    $submitAction->execute($this->record, $this->record->user);
                }
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}

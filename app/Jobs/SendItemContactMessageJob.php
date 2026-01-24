<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Job to send item contact message
 */
class SendItemContactMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Item $item,
        public string $senderName,
        public string $senderEmail,
        public string $message
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $itemOwner = $this->item->user;
            
            if (!$itemOwner || !$itemOwner->email) {
                Log::warning('Cannot send item contact message: item owner has no email', [
                    'item_id' => $this->item->id,
                ]);
                return;
            }

            // Send email to item owner
            Mail::send('emails.item-contact', [
                'item' => $this->item,
                'senderName' => $this->senderName,
                'senderEmail' => $this->senderEmail,
                'message' => $this->message,
            ], function ($message) use ($itemOwner) {
                $message->to($itemOwner->email, $itemOwner->name ?? '')
                        ->subject(__('items.email.contact_subject', ['title' => $this->item->title]));
            });

            Log::info('Item contact message sent successfully', [
                'item_id' => $this->item->id,
                'sender_email' => $this->senderEmail,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send item contact message', [
                'item_id' => $this->item->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

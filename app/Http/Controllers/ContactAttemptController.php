<?php

namespace App\Http\Controllers;

use App\Models\ContactAttempt;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ContactAttemptController extends Controller
{
    /**
     * Track a contact attempt (WhatsApp/Telegram click).
     * Designed to be called via Beacon API for non-blocking tracking.
     */
    public function track(Request $request): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'channel' => 'required|in:whatsapp,telegram',
        ]);

        // Only track for authenticated users
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Create the attempt record
        ContactAttempt::create([
            'user_id' => Auth::id(),
            'item_id' => $validated['item_id'],
            'channel' => $validated['channel'],
        ]);

        return response()->json(['success' => true]);
    }
}

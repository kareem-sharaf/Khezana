<?php

namespace App\Http\Controllers;

use App\Models\OfferAttempt;
use App\Models\Request as RequestModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OfferAttemptController extends Controller
{
    /**
     * Track an offer attempt (WhatsApp/Telegram click on request page).
     * Designed to be called via Beacon API for non-blocking tracking.
     */
    public function track(Request $request): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id',
            'channel' => 'required|in:whatsapp,telegram',
            'operation_type' => 'nullable|in:sell,rent,donate',
            'price' => 'nullable|numeric|min:0',
        ]);

        // Only track for authenticated users
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Create the attempt record
        OfferAttempt::create([
            'user_id' => Auth::id(),
            'request_id' => $validated['request_id'],
            'channel' => $validated['channel'],
            'operation_type' => $validated['operation_type'] ?? null,
            'price' => $validated['price'] ?? null,
        ]);

        return response()->json(['success' => true]);
    }
}

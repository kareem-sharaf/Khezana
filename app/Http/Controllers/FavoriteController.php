<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Request $request, int $itemId): RedirectResponse
    {
        $item = Item::findOrFail($itemId);
        $user = Auth::user();

        // TODO: Implement favorites table/model
        // $isFavorite = $user->favorites()->where('item_id', $itemId)->exists();
        
        // if ($isFavorite) {
        //     $user->favorites()->detach($itemId);
        //     $message = 'تم إزالة الإعلان من المفضلة.';
        // } else {
        //     $user->favorites()->attach($itemId);
        //     $message = 'تم إضافة الإعلان إلى المفضلة.';
        // }

        $message = 'تم تحديث المفضلة.';

        return redirect()->route('public.items.show', ['id' => $item->id, 'slug' => $item->slug])
            ->with('success', $message);
    }
}

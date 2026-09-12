<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Display current user's wishlist page.
     */
    public function index(): View
    {
        $wishlists = Wishlist::with('product.store')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('user.wishlist', compact('wishlists'));
    }

    /**
     * Toggle product favorite state via AJAX.
     */
    public function toggle(Request $request): JsonResponse
    {
        $productId = $request->input('product_id');
        $product = Product::findOrFail($productId);

        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $favorited = false;
            $msg = 'Đã xóa khỏi danh sách yêu thích';
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ]);
            $favorited = true;
            $msg = 'Đã thêm vào danh sách yêu thích!';
        }

        $count = Wishlist::where('user_id', auth()->id())->count();

        // Update user favorite_count cache
        auth()->user()->update(['favorite_count' => $count]);

        return response()->json([
            'success' => true,
            'favorited' => $favorited,
            'message' => $msg,
            'total_favorites' => $count,
        ]);
    }
}

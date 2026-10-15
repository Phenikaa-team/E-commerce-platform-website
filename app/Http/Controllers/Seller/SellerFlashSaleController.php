<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\FlashSaleCampaign;
use App\Models\FlashSaleItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SellerFlashSaleController extends Controller
{
    public function index(): View
    {
        $store = auth()->user()->store;
        $campaigns = FlashSaleCampaign::query()
            ->where('is_active', true)
            ->with(['items.product'])
            ->latest()
            ->get()
            ->filter(fn (FlashSaleCampaign $campaign): bool => $campaign->isOpenForApplicationsAt())
            ->values();
        $products = Product::query()
            ->with('flashSaleItems.campaign')
            ->where('store_id', $store->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('seller.flash-sales.index', compact('campaigns', 'products', 'store'));
    }

    public function apply(Request $request, int $id): RedirectResponse
    {
        $store = auth()->user()->store;
        $campaign = FlashSaleCampaign::findOrFail($id);
        if (! $campaign->isOpenForApplicationsAt()) {
            throw ValidationException::withMessages([
                'campaign' => 'Đợt Flash Sale này đã kết thúc hoặc đang tạm dừng.',
            ]);
        }

        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'flash_sale_price' => 'required|numeric|min:0',
        ]);
        $product = Product::query()
            ->where('store_id', $store->id)
            ->whereKey($data['product_id'])
            ->firstOrFail();
        $regularPrice = (float) $product->regular_selling_price;

        if ((float) $data['flash_sale_price'] >= $regularPrice) {
            throw ValidationException::withMessages([
                'flash_sale_price' => 'Giá Flash Sale phải thấp hơn giá bán thường của sản phẩm.',
            ]);
        }

        FlashSaleItem::updateOrCreate(
            [
                'flash_sale_campaign_id' => $campaign->id,
                'product_id' => $product->id,
            ],
            [
                'regular_price' => $regularPrice,
                'flash_sale_price' => $data['flash_sale_price'],
            ]
        );

        return back()->with('success', 'Đã đăng ký sản phẩm vào đợt Flash Sale.');
    }

    public function remove(int $id, int $itemId): RedirectResponse
    {
        $store = auth()->user()->store;
        $item = FlashSaleItem::query()
            ->where('flash_sale_campaign_id', $id)
            ->whereKey($itemId)
            ->whereHas('product', fn ($query) => $query->where('store_id', $store->id))
            ->firstOrFail();
        $item->delete();

        return back()->with('success', 'Đã rút sản phẩm khỏi đợt Flash Sale.');
    }
}

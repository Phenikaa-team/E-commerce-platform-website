<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FlashSaleCampaign;
use App\Models\FlashSaleItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class FlashSaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_flash_sale_uses_campaign_window_and_restores_regular_price_after_expiry(): void
    {
        $start = Carbon::parse('2026-10-15 10:00:00');
        $end = $start->copy()->addHour();
        $product = $this->createProduct([
            'price' => 100000,
            'original_price' => 120000,
        ]);
        $campaign = FlashSaleCampaign::create([
            'name' => 'Flash Sale theo chiến dịch',
            'schedule_type' => 'range',
            'starts_at' => $start,
            'ends_at' => $end,
            'is_active' => true,
        ]);
        FlashSaleItem::create([
            'flash_sale_campaign_id' => $campaign->id,
            'product_id' => $product->id,
            'regular_price' => 100000,
            'flash_sale_price' => 80000,
        ]);

        $this->travelTo($start->copy()->addMinutes(30));

        $activeProduct = $product->fresh();

        $this->assertTrue($activeProduct->isFlashSaleActive());
        $this->assertSame(80000.0, $activeProduct->effective_price);
        $this->assertSame(100000.0, $activeProduct->effective_original_price);
        $this->assertTrue(Product::activeFlashSale()->whereKey($product->id)->exists());

        $this->travelTo($end->copy());

        $expiredProduct = $product->fresh();

        $this->assertFalse($expiredProduct->isFlashSaleActive());
        $this->assertSame(100000.0, $expiredProduct->effective_price);
        $this->assertSame(120000.0, $expiredProduct->effective_original_price);
        $this->assertFalse(Product::activeFlashSale()->whereKey($product->id)->exists());
        $this->assertTrue(Product::withoutActiveFlashSale()->whereKey($product->id)->exists());

        $this->travelBack();
    }

    public function test_flash_sale_scope_excludes_future_campaigns_until_the_start_time(): void
    {
        $now = Carbon::parse('2026-10-15 10:00:00');
        $product = $this->createProduct([
            'price' => 200000,
        ]);
        $campaign = FlashSaleCampaign::create([
            'name' => 'Flash Sale sắp diễn ra',
            'schedule_type' => 'range',
            'starts_at' => $now->copy()->addHour(),
            'ends_at' => $now->copy()->addHours(2),
            'is_active' => true,
        ]);
        FlashSaleItem::create([
            'flash_sale_campaign_id' => $campaign->id,
            'product_id' => $product->id,
            'regular_price' => 200000,
            'flash_sale_price' => 150000,
        ]);

        $this->travelTo($now);

        $this->assertFalse($product->fresh()->isFlashSaleActive());
        $this->assertFalse(Product::activeFlashSale()->whereKey($product->id)->exists());
        $this->assertTrue(Product::withoutActiveFlashSale()->whereKey($product->id)->exists());

        $this->travelTo($now->copy()->addHour());

        $this->assertTrue($product->fresh()->isFlashSaleActive());
        $this->assertTrue(Product::activeFlashSale()->whereKey($product->id)->exists());
        $this->assertFalse(Product::withoutActiveFlashSale()->whereKey($product->id)->exists());

        $this->travelBack();
    }

    public function test_legacy_product_flash_sale_flag_does_not_activate_without_campaign_item(): void
    {
        $product = $this->createProduct([
            'price' => 100000,
            'is_flash_sale' => true,
            'flash_sale_price' => 80000,
            'flash_sale_starts_at' => now()->subHour(),
            'flash_sale_ends_at' => now()->addHour(),
        ]);

        $this->assertFalse($product->fresh()->isFlashSaleActive());
        $this->assertFalse(Product::activeFlashSale()->whereKey($product->id)->exists());
        $this->assertSame(100000.0, (float) $product->fresh()->effective_price);
    }

    public function test_seller_applies_product_to_admin_campaign_and_price_falls_back_after_expiry(): void
    {
        $seller = User::factory()->create();
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Seller Flash Store',
            'slug' => 'seller-flash-store-'.uniqid(),
            'status' => 'active',
        ]);
        $category = Category::create([
            'name' => 'Thiết bị',
            'slug' => 'thiet-bi-'.uniqid(),
        ]);

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Tai nghe Flash Sale',
            'slug' => 'tai-nghe-flash-sale-'.uniqid(),
            'price' => 90000,
            'original_price' => 100000,
            'stock' => 20,
            'status' => 'active',
        ]);
        $campaign = FlashSaleCampaign::create([
            'name' => 'Flash Sale 4 giờ',
            'schedule_type' => 'range',
            'starts_at' => now()->subMinute(),
            'ends_at' => now()->addHours(4),
            'is_active' => true,
        ]);

        $response = $this->actingAs($seller)->post(route('seller.flash-sales.products.store', $campaign->id), [
            'product_id' => $product->id,
            'flash_sale_price' => 80000,
        ]);

        $response->assertRedirect();
        $item = FlashSaleItem::where('flash_sale_campaign_id', $campaign->id)->where('product_id', $product->id)->firstOrFail();
        $this->assertSame(90000.0, (float) $item->regular_price);
        $this->assertSame(80000.0, (float) $item->flash_sale_price);
        $this->assertSame(80000.0, (float) $product->fresh()->effective_price);

        $this->travelTo($campaign->ends_at->copy());
        $this->assertSame(90000.0, (float) $product->fresh()->effective_price);
        $this->travelBack();
    }

    public function test_recurring_campaign_is_active_only_on_configured_day_and_time(): void
    {
        $campaign = FlashSaleCampaign::create([
            'name' => 'Ngày đôi',
            'schedule_type' => 'recurring',
            'recurring_dates' => ['10/15'],
            'daily_starts_at' => '10:00',
            'daily_ends_at' => '14:00',
            'is_active' => true,
        ]);

        $this->travelTo(Carbon::parse('2026-10-15 12:00:00'));
        $this->assertTrue($campaign->fresh()->isActiveAt());

        $this->travelTo(Carbon::parse('2026-10-16 12:00:00'));
        $this->assertFalse($campaign->fresh()->isActiveAt());
        $this->travelBack();
    }

    public function test_flash_sale_page_lists_only_products_from_active_campaigns(): void
    {
        $activeProduct = $this->createProduct([
            'name' => 'Sản phẩm Flash Sale đang chạy',
            'slug' => 'san-pham-flash-sale-dang-chay-'.uniqid(),
            'price' => 100000,
        ]);
        $inactiveProduct = $this->createProduct([
            'name' => 'Sản phẩm Flash Sale đã hết',
            'slug' => 'san-pham-flash-sale-da-het-'.uniqid(),
            'price' => 100000,
        ]);
        $campaign = FlashSaleCampaign::create([
            'name' => 'Flash Sale trang danh sách',
            'schedule_type' => 'range',
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHour(),
            'is_active' => true,
        ]);
        $expiredCampaign = FlashSaleCampaign::create([
            'name' => 'Flash Sale đã hết hạn',
            'schedule_type' => 'range',
            'starts_at' => now()->subHours(2),
            'ends_at' => now()->subHour(),
            'is_active' => true,
        ]);
        FlashSaleItem::create([
            'flash_sale_campaign_id' => $campaign->id,
            'product_id' => $activeProduct->id,
            'regular_price' => 100000,
            'flash_sale_price' => 80000,
        ]);
        FlashSaleItem::create([
            'flash_sale_campaign_id' => $expiredCampaign->id,
            'product_id' => $inactiveProduct->id,
            'regular_price' => 100000,
            'flash_sale_price' => 70000,
        ]);

        $response = $this->get(route('flash-sale.index'));

        $response->assertOk()
            ->assertViewIs('catalog.listing')
            ->assertSeeText('Sản phẩm Flash Sale đang chạy')
            ->assertDontSeeText('Sản phẩm Flash Sale đã hết');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createProduct(array $attributes): Product
    {
        $seller = User::factory()->create();
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Flash Sale Store',
            'slug' => 'flash-sale-store-'.uniqid(),
            'status' => 'active',
        ]);
        $category = Category::create([
            'name' => 'Điện tử',
            'slug' => 'dien-tu-'.uniqid(),
        ]);

        return Product::create(array_merge([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Flash Sale Product',
            'slug' => 'flash-sale-product-'.uniqid(),
            'stock' => 10,
            'status' => 'active',
        ], $attributes));
    }
}

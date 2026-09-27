<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductVariantInventoryTest extends TestCase
{
    use RefreshDatabase;

    private function createProductWithVariants(): Product
    {
        $user = User::factory()->create();
        $store = Store::create([
            'user_id' => $user->id,
            'name' => 'Apple Store',
            'slug' => 'apple-store-'.uniqid(),
        ]);
        $category = Category::create([
            'name' => 'Điện Thoại',
            'slug' => 'dien-thoai-'.uniqid(),
        ]);

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'iPhone 15 Pro',
            'slug' => 'iphone-15-pro-'.uniqid(),
            'price' => 28000000,
            'original_price' => 30000000,
            'stock' => 50,
            'sold_count' => 0,
            'status' => 'active',
            'main_image_url' => 'https://example.com/iphone.jpg',
            'variants' => [
                'colors' => [
                    ['label' => 'Titan Tự Nhiên', 'image' => 'https://example.com/natural.jpg'],
                    ['label' => 'Titan Xanh', 'image' => 'https://example.com/blue.jpg'],
                ],
                'options' => ['128GB', '256GB'],
            ],
        ]);

        $product->syncVariantsFromAttribute();

        return $product;
    }

    public function test_product_detail_page_renders_variants_and_json_data(): void
    {
        $product = $this->createProductWithVariants();

        $response = $this->get(route('product.detail', $product->slug));
        $response->assertStatus(200);
        $response->assertSee('Titan Tự Nhiên');
        $response->assertSee('128GB');
        $response->assertSee('pd-variants-json');
        $response->assertSee('data-color-name="Titan Tự Nhiên"', false);
        $response->assertSee('data-option-name="128GB"', false);
    }

    public function test_variant_stock_is_validated_when_adding_to_cart(): void
    {
        $user = User::factory()->create();
        $product = $this->createProductWithVariants();

        // Set variant 1 stock to 2
        $variant = $product->productVariants()->where('name', 'Titan Tự Nhiên - 128GB')->first();
        $this->assertNotNull($variant);
        $variant->update(['stock' => 2]);

        // Trying to add 5 items should fail with 422
        $response = $this->actingAs($user)->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 5,
            'variant' => 'Titan Tự Nhiên - 128GB',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'success' => false,
        ]);

        // Adding 2 items should succeed
        $successResponse = $this->actingAs($user)->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
            'variant' => 'Titan Tự Nhiên - 128GB',
        ]);

        $successResponse->assertStatus(200);
        $successResponse->assertJsonFragment([
            'success' => true,
        ]);
    }

    public function test_variant_stock_decrements_upon_order_and_restores_upon_cancellation(): void
    {
        $buyer = User::factory()->create();
        $product = $this->createProductWithVariants();

        $variant = $product->productVariants()->where('name', 'Titan Tự Nhiên - 128GB')->first();
        $variant->update(['stock' => 10]);
        $product->update(['stock' => 10]);

        // Place an order
        $order = Order::create([
            'order_code' => 'ORD-'.strtoupper(uniqid()),
            'user_id' => $buyer->id,
            'store_id' => $product->store_id,
            'status' => 'pending',
            'subtotal' => 28000000,
            'shipping_fee' => 0,
            'discount_amount' => 0,
            'total' => 28000000,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'shipping_address' => ['name' => 'Tester', 'phone' => '0901234567', 'address' => 'HN'],
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'selected_variant' => 'Titan Tự Nhiên - 128GB',
            'quantity' => 3,
            'unit_price' => 28000000,
            'subtotal' => 84000000,
        ]);

        // Simulate decrementing stock upon order
        $variant->decrement('stock', 3);
        $this->assertEquals(7, $variant->fresh()->stock);

        // Cancel order via BuyerOrderController
        $cancelResponse = $this->actingAs($buyer)->post(route('user.orders.cancel', $order->order_code));
        $cancelResponse->assertRedirect();

        // Variant stock should be restored to 10
        $this->assertEquals(10, $variant->fresh()->stock);
    }

    public function test_seller_product_creation_syncs_product_variants_table(): void
    {
        $seller = User::factory()->create();
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Fashion Shop',
            'slug' => 'fashion-shop-'.uniqid(),
            'status' => 'active',
        ]);
        $category = Category::create([
            'name' => 'Áo Thun',
            'slug' => 'ao-thun-'.uniqid(),
        ]);

        $response = $this->actingAs($seller)->post(route('seller.products.store'), [
            'name' => 'Áo Thun Cao Cấp',
            'category_id' => $category->id,
            'price' => 200000,
            'stock' => 100,
            'description' => 'Áo thun cotton cao cấp',
            'color_variants' => 'Đen, Trắng',
            'size_variants' => 'M, L',
        ]);

        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('name', 'Áo Thun Cao Cấp')->first();
        $this->assertNotNull($product);

        $variants = ProductVariant::where('product_id', $product->id)->get();
        $this->assertCount(4, $variants); // 2 colors x 2 sizes = 4 variants
        $this->assertTrue($variants->contains('name', 'Đen - M'));
        $this->assertTrue($variants->contains('name', 'Trắng - L'));
        $this->assertGreaterThan(0, $variants->first()->stock);
    }

    public function test_seller_can_set_custom_prices_per_variant_and_cart_calculates_correctly(): void
    {
        $seller = User::factory()->create();
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Apple Store Official',
            'slug' => 'apple-store-'.uniqid(),
            'status' => 'active',
        ]);
        $category = Category::create([
            'name' => 'Điện Thoại',
            'slug' => 'dien-thoai-'.uniqid(),
        ]);

        // Seller sets 512GB at 34,990,000đ and 1TB at 41,990,000đ, with limited edition color +1,000,000đ
        $customVariantsMatrix = [
            [
                'name' => 'Titan Tự Nhiên - 512GB',
                'color' => 'Titan Tự Nhiên',
                'option' => '512GB',
                'price' => 34990000,
                'original_price' => 38990000,
                'stock' => 15,
                'sku' => 'IP15-NAT-512',
            ],
            [
                'name' => 'Titan Tự Nhiên - 1TB',
                'color' => 'Titan Tự Nhiên',
                'option' => '1TB',
                'price' => 41990000,
                'original_price' => 46990000,
                'stock' => 10,
                'sku' => 'IP15-NAT-1TB',
            ],
            [
                'name' => 'Titan Sa Mạc (Bản giới hạn) - 1TB',
                'color' => 'Titan Sa Mạc (Bản giới hạn)',
                'option' => '1TB',
                'price' => 42990000,
                'original_price' => 47990000,
                'stock' => 5,
                'sku' => 'IP15-DES-1TB',
            ],
        ];

        $response = $this->actingAs($seller)->post(route('seller.products.store'), [
            'name' => 'iPhone 16 Pro Max',
            'category_id' => $category->id,
            'price' => 34990000,
            'original_price' => 38990000,
            'stock' => 30,
            'description' => 'iPhone 16 Pro Max chính hãng',
            'color_variants' => 'Titan Tự Nhiên, Titan Sa Mạc (Bản giới hạn)',
            'size_variants' => '512GB, 1TB',
            'variants_data' => json_encode($customVariantsMatrix),
        ]);

        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('name', 'iPhone 16 Pro Max')->first();
        $this->assertNotNull($product);

        $var512 = $product->productVariants()->where('name', 'Titan Tự Nhiên - 512GB')->first();
        $var1tb = $product->productVariants()->where('name', 'Titan Tự Nhiên - 1TB')->first();
        $varLimited = $product->productVariants()->where('name', 'Titan Sa Mạc (Bản giới hạn) - 1TB')->first();

        $this->assertNotNull($var512);
        $this->assertNotNull($var1tb);
        $this->assertNotNull($varLimited);

        // Verify 512GB is cheaper than 1TB
        $this->assertEquals(34990000, (float) $var512->price);
        $this->assertEquals(41990000, (float) $var1tb->price);
        $this->assertLessThan((float) $var1tb->price, (float) $var512->price);

        // Verify limited edition color is more expensive than standard color
        $this->assertEquals(42990000, (float) $varLimited->price);
        $this->assertGreaterThan((float) $var1tb->price, (float) $varLimited->price);

        // Test Buyer Cart interactions
        $buyer = User::factory()->create();

        // 1. Add 512GB to cart
        $cartAdd512 = $this->actingAs($buyer)->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
            'variant' => 'Titan Tự Nhiên - 512GB',
        ]);
        $cartAdd512->assertStatus(200);

        $cartItem = $buyer->cart->items()->where('selected_variant', 'Titan Tự Nhiên - 512GB')->first();
        $this->assertNotNull($cartItem);
        $this->assertEquals(34990000, (float) $cartItem->unit_price);

        // 2. Add 1TB to cart
        $cartAdd1tb = $this->actingAs($buyer)->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
            'variant' => 'Titan Tự Nhiên - 1TB',
        ]);
        $cartAdd1tb->assertStatus(200);

        $cartItem1tb = $buyer->cart->items()->where('selected_variant', 'Titan Tự Nhiên - 1TB')->first();
        $this->assertNotNull($cartItem1tb);
        $this->assertEquals(41990000, (float) $cartItem1tb->unit_price);

        // 3. Switch variant inside cart from 512GB to Limited Edition 1TB
        $updateVariantResp = $this->actingAs($buyer)->postJson(route('cart.item.variant', $cartItem->id), [
            'variant' => 'Titan Sa Mạc (Bản giới hạn) - 1TB',
        ]);
        $updateVariantResp->assertStatus(200);
        $updateVariantResp->assertJsonFragment([
            'unit_price' => 42990000,
        ]);

        $this->assertEquals(42990000, (float) $cartItem->fresh()->unit_price);
    }
}

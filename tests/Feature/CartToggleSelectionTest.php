<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartToggleSelectionTest extends TestCase
{
    use RefreshDatabase;

    private function createProductWithStore(Store $store, int $price = 100000): Product
    {
        $category = Category::create([
            'name' => 'Category '.uniqid(),
            'slug' => 'cat-'.uniqid(),
        ]);

        return Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Product '.uniqid(),
            'slug' => 'product-'.uniqid(),
            'price' => $price,
            'stock' => 10,
            'sold_count' => 0,
            'status' => 'active',
            'main_image_url' => 'https://example.com/test.jpg',
        ]);
    }

    public function test_can_uncheck_and_check_single_item_in_cart(): void
    {
        $user = User::factory()->create();
        $store = Store::create([
            'user_id' => $user->id,
            'name' => 'Shop 1',
            'slug' => 'shop-1',
        ]);
        $prod1 = $this->createProductWithStore($store, 100000);
        $prod2 = $this->createProductWithStore($store, 200000);

        $cart = Cart::create(['user_id' => $user->id]);
        $item1 = $cart->items()->create([
            'product_id' => $prod1->id,
            'quantity' => 1,
            'unit_price' => 100000,
            'is_selected' => true,
        ]);
        $item2 = $cart->items()->create([
            'product_id' => $prod2->id,
            'quantity' => 1,
            'unit_price' => 200000,
            'is_selected' => true,
        ]);

        $this->actingAs($user);

        // Uncheck item 1
        $response = $this->postJson('/cart/toggle-select', [
            'type' => 'item',
            'item_id' => $item1->id,
            'is_selected' => false,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'selected_count' => 1,
            ]);

        $this->assertFalse((bool) $item1->fresh()->is_selected);
        $this->assertTrue((bool) $item2->fresh()->is_selected);

        // Check item 1 back
        $response2 = $this->postJson('/cart/toggle-select', [
            'type' => 'item',
            'item_id' => $item1->id,
            'is_selected' => true,
        ]);

        $response2->assertOk()
            ->assertJson([
                'success' => true,
                'selected_count' => 2,
            ]);

        $this->assertTrue((bool) $item1->fresh()->is_selected);
    }

    public function test_can_toggle_select_all_and_store(): void
    {
        $user = User::factory()->create();
        $store = Store::create([
            'user_id' => $user->id,
            'name' => 'Shop 2',
            'slug' => 'shop-2',
        ]);
        $prod1 = $this->createProductWithStore($store, 150000);
        $prod2 = $this->createProductWithStore($store, 250000);

        $cart = Cart::create(['user_id' => $user->id]);
        $item1 = $cart->items()->create([
            'product_id' => $prod1->id,
            'quantity' => 1,
            'unit_price' => 150000,
            'is_selected' => true,
        ]);
        $item2 = $cart->items()->create([
            'product_id' => $prod2->id,
            'quantity' => 1,
            'unit_price' => 250000,
            'is_selected' => true,
        ]);

        $this->actingAs($user);

        // Toggle all off
        $resAllOff = $this->postJson('/cart/toggle-select', [
            'type' => 'all',
            'is_selected' => false,
        ]);

        $resAllOff->assertOk()->assertJson(['success' => true, 'selected_count' => 0]);
        $this->assertFalse((bool) $item1->fresh()->is_selected);
        $this->assertFalse((bool) $item2->fresh()->is_selected);

        // Toggle store on
        $resStoreOn = $this->postJson('/cart/toggle-select', [
            'type' => 'store',
            'store_id' => $store->id,
            'is_selected' => true,
        ]);

        $resStoreOn->assertOk()->assertJson(['success' => true, 'selected_count' => 2]);
        $this->assertTrue((bool) $item1->fresh()->is_selected);
        $this->assertTrue((bool) $item2->fresh()->is_selected);
    }
}

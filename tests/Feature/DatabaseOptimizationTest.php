<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_performance_indexes_are_registered(): void
    {
        $productIndexes = array_column(DB::select('PRAGMA index_list(products)'), 'name');
        $this->assertContains('products_store_id_index', $productIndexes);
        $this->assertContains('products_brand_index', $productIndexes);
        $this->assertContains('products_status_price_index', $productIndexes);
        $this->assertContains('products_status_sold_count_index', $productIndexes);

        $orderIndexes = array_column(DB::select('PRAGMA index_list(orders)'), 'name');
        $this->assertContains('orders_user_id_status_created_at_index', $orderIndexes);
        $this->assertContains('orders_store_id_status_created_at_index', $orderIndexes);
        $this->assertContains('orders_payment_method_status_index', $orderIndexes);

        $orderItemIndexes = array_column(DB::select('PRAGMA index_list(order_items)'), 'name');
        $this->assertContains('order_items_order_id_index', $orderItemIndexes);
        $this->assertContains('order_items_product_id_index', $orderItemIndexes);
    }

    public function test_store_performance_overview_aggregated_query(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Optimization Test Store',
            'slug' => 'optimization-test-store',
            'status' => 'active',
        ]);

        $buyer = User::factory()->create();

        // Create completed order for today
        Order::create([
            'order_code' => 'ORD-OPT-1',
            'user_id' => $buyer->id,
            'store_id' => $store->id,
            'status' => 'completed',
            'subtotal' => 150000,
            'total' => 170000,
            'shipping_address' => ['name' => 'Buyer', 'phone' => '0912345678', 'address' => 'Hanoi'],
            'created_at' => now(),
        ]);

        // Create cancelled order (should not be in revenue)
        Order::create([
            'order_code' => 'ORD-OPT-2',
            'user_id' => $buyer->id,
            'store_id' => $store->id,
            'status' => 'cancelled',
            'subtotal' => 80000,
            'total' => 100000,
            'shipping_address' => ['name' => 'Buyer', 'phone' => '0912345678', 'address' => 'Hanoi'],
            'created_at' => now(),
        ]);

        $overview = $store->getPerformanceOverview(7);

        $this->assertEquals(2, $overview['stats']['total_orders']);
        $this->assertEquals(150000, $overview['revenue30d']);
        $this->assertCount(7, $overview['chartLabels']);
        $this->assertCount(7, $overview['chartOrders']);
        $this->assertCount(7, $overview['chartRevenue']);
        // The most recent day (today) should have 2 orders and 150000 revenue
        $this->assertEquals(2, end($overview['chartOrders']));
        $this->assertEquals(150000, end($overview['chartRevenue']));
    }

    public function test_buyer_order_history_with_status_grouping(): void
    {
        $user = User::factory()->create();
        $store = Store::create([
            'user_id' => $user->id,
            'name' => 'Test Store',
            'slug' => 'test-store-buyer-hist',
            'status' => 'active',
        ]);

        Order::create([
            'order_code' => 'ORD-B1',
            'user_id' => $user->id,
            'store_id' => $store->id,
            'status' => 'pending',
            'subtotal' => 100000,
            'total' => 100000,
            'shipping_address' => ['name' => 'Test', 'phone' => '0901', 'address' => 'Addr'],
        ]);

        Order::create([
            'order_code' => 'ORD-B2',
            'user_id' => $user->id,
            'store_id' => $store->id,
            'status' => 'completed',
            'subtotal' => 200000,
            'total' => 200000,
            'shipping_address' => ['name' => 'Test', 'phone' => '0901', 'address' => 'Addr'],
        ]);

        $response = $this->actingAs($user)->get(route('user.orders'));
        $response->assertStatus(200);
        $response->assertViewHas('counts', function ($counts) {
            return $counts['all'] === 2 && $counts['pending'] === 1 && $counts['completed'] === 1 && $counts['cancelled'] === 0;
        });
    }

    public function test_admin_dashboard_renders_with_optimized_queries(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('sevenDaysLabels');
        $response->assertViewHas('sevenDaysRevenue');
        $response->assertViewHas('statusCounts');
    }
}

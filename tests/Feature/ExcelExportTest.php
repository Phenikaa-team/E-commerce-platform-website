<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExcelExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $sellerUser;

    protected Store $store;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_test@shopmart.vn',
        ]);

        $this->sellerUser = User::factory()->create([
            'role' => 'seller',
            'email' => 'seller_test@shopmart.vn',
        ]);

        $this->store = Store::create([
            'user_id' => $this->sellerUser->id,
            'name' => 'Gian Hàng Công Nghệ Test',
            'slug' => 'gian-hang-cong-nghe-test',
            'status' => 'active',
        ]);

        $this->customer = User::factory()->create([
            'role' => 'user',
            'email' => 'customer_test@shopmart.vn',
        ]);
    }

    public function test_guest_cannot_export_seller_orders(): void
    {
        $response = $this->get(route('seller.orders.export'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_export_admin_orders(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.orders.export'));
        // Non-admin should be forbidden or redirected
        $this->assertTrue(in_array($response->status(), [403, 302]));
    }

    public function test_seller_can_export_orders_as_styled_excel(): void
    {
        $category = Category::create([
            'name' => 'Điện tử',
            'slug' => 'dien-tu',
        ]);

        $product = Product::create([
            'store_id' => $this->store->id,
            'category_id' => $category->id,
            'name' => 'Tai nghe chống ồn Pro Test',
            'slug' => 'tai-nghe-pro-test',
            'price' => 1500000,
            'stock' => 10,
            'main_image_url' => '/storage/products/test.jpg',
            'status' => 'active',
        ]);

        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_code' => 'SM-TEST-EXCEL-01',
            'status' => 'pending',
            'subtotal' => 1500000,
            'discount_amount' => 50000,
            'shipping_fee' => 30000,
            'total' => 1480000,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'shipping_address' => [
                'name' => 'Trần Văn Test',
                'phone' => '0988776655',
                'address' => '123 Đường Test, Hà Nội',
            ],
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 1500000,
            'subtotal' => 1500000,
        ]);

        $response = $this->actingAs($this->sellerUser)->get(route('seller.orders.export'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename=', $response->headers->get('Content-Disposition') ?? '');

        $content = $response->streamedContent();
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
        $this->assertStringContainsString('urn:schemas-microsoft-com:office:spreadsheet', $content);
        $this->assertStringContainsString('SM-TEST-EXCEL-01', $content);
        $this->assertStringContainsString('Trần Văn Test', $content);
        $this->assertStringContainsString('Tai nghe chống ồn Pro Test', $content);
    }

    public function test_seller_can_export_products_as_styled_excel(): void
    {
        $category = Category::create([
            'name' => 'Gia dụng',
            'slug' => 'gia-dung',
        ]);

        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $category->id,
            'name' => 'Nồi chiên không dầu Smart 5L',
            'slug' => 'noi-chien-smart-5l',
            'price' => 2200000,
            'original_price' => 2500000,
            'stock' => 15,
            'sold_count' => 8,
            'main_image_url' => '/storage/products/test2.jpg',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->sellerUser)->get(route('seller.products.export'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename=', $response->headers->get('Content-Disposition') ?? '');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Nồi chiên không dầu Smart 5L', $content);
        $this->assertStringContainsString('Gia dụng', $content);
    }

    public function test_admin_can_export_all_platform_orders(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.orders.export'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename=', $response->headers->get('Content-Disposition') ?? '');

        $content = $response->streamedContent();
        $this->assertStringContainsString('urn:schemas-microsoft-com:office:spreadsheet', $content);
        $this->assertStringContainsString('BÁO CÁO TOÀN DIỆN ĐƠN HÀNG HỆ THỐNG', $content);
    }

    public function test_admin_can_export_users_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.export'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('BÁO CÁO DANH SÁCH NGƯỜI DÙNG', $content);
        $this->assertStringContainsString('admin_test@shopmart.vn', $content);
        $this->assertStringContainsString('seller_test@shopmart.vn', $content);
    }

    public function test_admin_can_export_financial_revenue_report(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.revenue.export'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('BÁO CÁO TÀI CHÍNH &amp; DOANH THU NỀN TẢNG', $content);
    }
}

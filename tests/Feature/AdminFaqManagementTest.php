<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFaqManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->user = User::factory()->create([
            'role' => 'user',
        ]);
    }

    public function test_guest_and_user_cannot_access_admin_faqs(): void
    {
        $this->get(route('admin.faqs.index'))->assertRedirect(route('login'));

        $this->actingAs($this->user)
            ->get(route('admin.faqs.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_faqs_list_and_create_faq(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.faqs.index'))
            ->assertOk()
            ->assertSee('Quản lý câu hỏi trợ giúp (FAQ)');

        $createResponse = $this->actingAs($this->admin)
            ->post(route('admin.faqs.store'), [
                'category' => 'orders',
                'question' => 'Làm sao để hủy đơn hàng nhanh nhất?',
                'answer' => 'Vào đơn hàng của tôi và bấm nút Hủy đơn khi shop chưa xác nhận.',
                'prompt' => 'Cách hủy đơn hàng nhanh',
                'sort_order' => 1,
                'is_active' => '1',
            ]);

        $createResponse->assertRedirect(route('admin.faqs.index'));

        $this->assertDatabaseHas('faqs', [
            'question' => 'Làm sao để hủy đơn hàng nhanh nhất?',
            'category' => 'orders',
            'is_active' => 1,
        ]);
    }

    public function test_admin_can_update_and_toggle_faq(): void
    {
        $faq = Faq::create([
            'category' => 'payment',
            'question' => 'Có hỗ trợ trả góp không?',
            'answer' => 'Hiện tại chưa hỗ trợ trả góp.',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.faqs.update', $faq->id), [
                'category' => 'payment',
                'question' => 'Có hỗ trợ trả góp không? (Đã cập nhật)',
                'answer' => 'Có hỗ trợ trả góp 0% qua thẻ tín dụng.',
                'sort_order' => 5,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.faqs.index'));

        $this->assertDatabaseHas('faqs', [
            'id' => $faq->id,
            'question' => 'Có hỗ trợ trả góp không? (Đã cập nhật)',
        ]);

        // Toggle status
        $this->actingAs($this->admin)
            ->from(route('admin.faqs.index'))
            ->patch(route('admin.faqs.toggle', $faq->id))
            ->assertRedirect(route('admin.faqs.index'));

        $this->assertFalse((bool) $faq->fresh()->is_active);
    }

    public function test_admin_can_delete_faq(): void
    {
        $faq = Faq::create([
            'category' => 'shipping',
            'question' => 'Phí ship tính thế nào?',
            'answer' => 'Tính theo khoảng cách và khối lượng hàng.',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.faqs.destroy', $faq->id))
            ->assertRedirect(route('admin.faqs.index'));

        $this->assertDatabaseMissing('faqs', [
            'id' => $faq->id,
        ]);
    }

    public function test_public_support_page_renders_database_faqs(): void
    {
        Faq::create([
            'category' => 'orders',
            'question' => 'Câu hỏi test xuất hiện ở trang public support',
            'answer' => 'Nội dung trả lời test',
            'prompt' => 'Prompt test',
            'is_active' => true,
        ]);

        $response = $this->get(route('support.index'));
        $response->assertOk()
            ->assertSee('Câu hỏi test xuất hiện ở trang public support');
    }

    public function test_seller_product_form_does_not_contain_vat_field(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        Store::create([
            'user_id' => $seller->id,
            'name' => 'Cửa Hàng Test',
            'slug' => 'cua-hang-test',
            'status' => 'active',
        ]);

        $response = $this->actingAs($seller)->get(route('seller.products.create'));
        $response->assertOk();
        $response->assertDontSee('Thuế VAT');
        $response->assertDontSee('data-select-for="tax_rate"');
    }
}

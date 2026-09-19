<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $user;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@shopmart.vn',
        ]);

        $this->user = User::factory()->create();

        $store = Store::create([
            'user_id' => $this->user->id,
            'name' => 'Store Test',
            'slug' => 'store-test',
        ]);

        $category = Category::create([
            'name' => 'Công nghệ',
            'slug' => 'cong-nghe',
        ]);

        $this->product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Laptop Gaming Ultra',
            'slug' => 'laptop-gaming-ultra',
            'price' => 25000000,
            'stock' => 5,
            'status' => 'active',
        ]);
    }

    public function test_non_admin_cannot_access_reviews_moderation(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.reviews.index'));
        $this->assertTrue(in_array($response->status(), [403, 302]));
    }

    public function test_admin_can_view_and_moderate_review(): void
    {
        $review = Review::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'rating' => 4,
            'comment' => 'Máy dùng tốt nhưng quạt hơi ồn',
            'status' => 'pending',
        ]);

        // 1. Admin views index
        $res = $this->actingAs($this->admin)->get(route('admin.reviews.index'));
        $res->assertOk()
            ->assertSee('Máy dùng tốt nhưng quạt hơi ồn');

        // 2. Admin approves review
        $resApprove = $this->actingAs($this->admin)->post(route('admin.reviews.status', $review->id), [
            'status' => 'approved',
        ]);
        $resApprove->assertSessionHas('success');
        $this->assertEquals('approved', $review->fresh()->status);

        // 3. Admin deletes review
        $resDelete = $this->actingAs($this->admin)->delete(route('admin.reviews.destroy', $review->id));
        $resDelete->assertSessionHas('success');
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }
}

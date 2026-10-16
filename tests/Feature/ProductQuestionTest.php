<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductQuestion;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductQuestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_ask_question_on_product(): void
    {
        $seller = User::factory()->create();
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Tech Store',
            'slug' => 'tech-store',
            'status' => 'active',
        ]);
        $category = Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
        ]);
        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'MacBook Pro',
            'slug' => 'macbook-pro',
            'price' => 20000000,
            'status' => 'active',
            'stock' => 10,
        ]);

        $response = $this->post(route('product.questions.store', $product->id), [
            'customer_name' => 'Nguyễn Văn A',
            'question' => 'Sản phẩm này có được bảo hành chính hãng Apple Việt Nam không?',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_questions', [
            'product_id' => $product->id,
            'customer_name' => 'Nguyễn Văn A',
            'question' => 'Sản phẩm này có được bảo hành chính hãng Apple Việt Nam không?',
            'is_visible' => true,
        ]);
    }

    public function test_seller_can_answer_question_of_their_product(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Tech Store',
            'slug' => 'tech-store',
            'status' => 'active',
        ]);
        $seller->update(['store_id' => $store->id]);

        $category = Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
        ]);
        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'MacBook Pro',
            'slug' => 'macbook-pro',
            'price' => 20000000,
            'status' => 'active',
            'stock' => 10,
        ]);

        $question = ProductQuestion::create([
            'product_id' => $product->id,
            'customer_name' => 'Khách hỏi',
            'question' => 'Máy có sẵn màu xám không?',
            'is_visible' => true,
        ]);

        $response = $this->actingAs($seller)->post(route('seller.questions.answer', $question->id), [
            'answer' => 'Chào bạn, hiện shop đang sẵn cả màu xám và bạc nhé!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_questions', [
            'id' => $question->id,
            'answer' => 'Chào bạn, hiện shop đang sẵn cả màu xám và bạc nhé!',
        ]);
    }

    public function test_seller_can_hide_and_unhide_question(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Tech Store',
            'slug' => 'tech-store',
            'status' => 'active',
        ]);
        $seller->update(['store_id' => $store->id]);

        $category = Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
        ]);
        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'MacBook Pro',
            'slug' => 'macbook-pro',
            'price' => 20000000,
            'status' => 'active',
            'stock' => 10,
        ]);

        $question = ProductQuestion::create([
            'product_id' => $product->id,
            'customer_name' => 'Khách hỏi',
            'question' => 'Spam câu hỏi linh tinh',
            'is_visible' => true,
        ]);

        // Hide question
        $response = $this->actingAs($seller)->post(route('seller.questions.toggle-visibility', $question->id));
        $response->assertRedirect();
        $this->assertDatabaseHas('product_questions', [
            'id' => $question->id,
            'is_visible' => false,
        ]);

        // Unhide question
        $response = $this->actingAs($seller)->post(route('seller.questions.toggle-visibility', $question->id));
        $response->assertRedirect();
        $this->assertDatabaseHas('product_questions', [
            'id' => $question->id,
            'is_visible' => true,
        ]);
    }
}

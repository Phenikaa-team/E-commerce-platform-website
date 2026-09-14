<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test user can upload an avatar file and old avatar file is deleted.
     */
    public function test_user_can_upload_avatar_and_old_avatar_is_deleted_from_disk(): void
    {
        $user = User::factory()->create();

        // 1. First avatar upload
        $firstAvatar = UploadedFile::fake()->create('avatar1.jpg', 100, 'image/jpeg');
        $response = $this->actingAs($user)->post('/profile/update', [
            'name' => 'Test User',
            'avatar' => $firstAvatar,
        ]);

        $response->assertSessionHas('success');
        $user->refresh();

        $this->assertNotNull($user->avatar_url);
        $firstPath = str_replace('/storage/', '', $user->avatar_url);
        Storage::disk('public')->assertExists($firstPath);

        // 2. Second avatar upload (should delete first avatar from disk)
        $secondAvatar = UploadedFile::fake()->create('avatar2.png', 120, 'image/png');
        $response2 = $this->actingAs($user)->post('/profile/update', [
            'name' => 'Test User Updated',
            'avatar' => $secondAvatar,
        ]);

        $response2->assertSessionHas('success');
        $user->refresh();

        $secondPath = str_replace('/storage/', '', $user->avatar_url);
        $this->assertNotEquals($firstPath, $secondPath);
        Storage::disk('public')->assertExists($secondPath);
        Storage::disk('public')->assertMissing($firstPath);
    }

    /**
     * Test avatar upload validation rejects non-image files.
     */
    public function test_avatar_upload_rejects_invalid_file_types(): void
    {
        $user = User::factory()->create();
        $invalidFile = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post('/profile/update', [
            'name' => 'Test User',
            'avatar' => $invalidFile,
        ]);

        $response->assertSessionHasErrors('avatar');
    }

    /**
     * Test admin can upload avatar.
     */
    public function test_admin_can_upload_avatar(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $avatarFile = UploadedFile::fake()->create('admin_avatar.webp', 100, 'image/webp');

        $response = $this->actingAs($admin)->put('/admin/profile', [
            'name' => 'Super Admin',
            'email' => $admin->email,
            'avatar' => $avatarFile,
        ]);

        $response->assertRedirect(route('admin.profile'));
        $admin->refresh();

        $this->assertNotNull($admin->avatar_url);
        $path = str_replace('/storage/', '', $admin->avatar_url);
        Storage::disk('public')->assertExists($path);
    }

    /**
     * Test seller can upload store logo, banner, and personal avatar.
     */
    public function test_seller_can_upload_store_logo_banner_and_avatar(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Apple Store Flagship',
            'slug' => 'apple-store-flagship',
            'status' => 'active',
        ]);

        $logoFile = UploadedFile::fake()->create('store_logo.png', 100, 'image/png');
        $bannerFile = UploadedFile::fake()->create('store_banner.jpg', 200, 'image/jpeg');
        $userAvatar = UploadedFile::fake()->create('seller_avatar.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($seller)->put('/seller/profile', [
            'user_name' => 'Seller Leader',
            'store_name' => 'Apple Store Official',
            'status' => 'active',
            'store_logo' => $logoFile,
            'store_banner' => $bannerFile,
            'user_avatar' => $userAvatar,
        ]);

        $response->assertRedirect(route('seller.profile'));
        $store->refresh();
        $seller->refresh();

        // Check logo
        $logoPath = str_replace('/storage/', '', $store->logo_url);
        Storage::disk('public')->assertExists($logoPath);

        // Check banner
        $bannerPath = str_replace('/storage/', '', $store->banner_url);
        Storage::disk('public')->assertExists($bannerPath);

        // Check seller user avatar
        $avatarPath = str_replace('/storage/', '', $seller->avatar_url);
        Storage::disk('public')->assertExists($avatarPath);
    }

    /**
     * Test seller can create a product with main image and multiple gallery images.
     */
    public function test_seller_can_create_product_with_main_image_and_gallery(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create(['user_id' => $seller->id, 'name' => 'Tech Shop', 'slug' => 'tech-shop']);
        $category = Category::create(['name' => 'Điện Thoại', 'slug' => 'dien-thoai']);

        $mainImage = UploadedFile::fake()->create('iphone_main.jpg', 150, 'image/jpeg');
        $galleryImg1 = UploadedFile::fake()->create('iphone_side.jpg', 150, 'image/jpeg');
        $galleryImg2 = UploadedFile::fake()->create('iphone_back.jpg', 150, 'image/jpeg');

        $response = $this->actingAs($seller)->post('/seller/products', [
            'name' => 'iPhone 15 Pro Max',
            'category_id' => $category->id,
            'price' => 29000000,
            'stock' => 50,
            'description' => 'Flagship smartphone from Apple.',
            'main_image' => $mainImage,
            'images' => [$galleryImg1, $galleryImg2],
        ]);

        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('slug', 'like', 'iphone-15-pro-max%')->firstOrFail();
        $this->assertNotNull($product->main_image_url);

        $mainPath = str_replace('/storage/', '', $product->main_image_url);
        Storage::disk('public')->assertExists($mainPath);

        $this->assertEquals(2, $product->images()->count());
        foreach ($product->images as $img) {
            $imgPath = str_replace('/storage/', '', $img->url);
            Storage::disk('public')->assertExists($imgPath);
        }
    }

    /**
     * Test seller can delete an individual gallery image from product.
     */
    public function test_seller_can_delete_individual_gallery_image(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create(['user_id' => $seller->id, 'name' => 'Tech Shop', 'slug' => 'tech-shop']);
        $category = Category::create(['name' => 'Phụ Kiện', 'slug' => 'phu-kien']);

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Cáp Sạc Nhanh',
            'slug' => 'cap-sac-nhanh',
            'price' => 150000,
            'stock' => 100,
            'description' => 'Cáp sạc type-C siêu bền.',
        ]);

        // Upload a gallery image
        $galleryFile = UploadedFile::fake()->create('cable.jpg', 100, 'image/jpeg');
        $path = $galleryFile->store('products', 'public');
        $galleryImage = ProductImage::create([
            'product_id' => $product->id,
            'url' => '/storage/'.$path,
            'sort_order' => 1,
        ]);

        Storage::disk('public')->assertExists($path);

        // Delete gallery image
        $response = $this->actingAs($seller)->delete("/seller/products/{$product->id}/images/{$galleryImage->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('product_images', ['id' => $galleryImage->id]);
        Storage::disk('public')->assertMissing($path);
    }

    /**
     * Test deleting a product also deletes stored image files.
     */
    public function test_deleting_product_cleans_up_stored_files(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::create(['user_id' => $seller->id, 'name' => 'Gadgets', 'slug' => 'gadgets']);
        $category = Category::create(['name' => 'Tai Nghe', 'slug' => 'tai-nghe']);

        $mainFile = UploadedFile::fake()->create('headphone.jpg', 100, 'image/jpeg');
        $mainPath = $mainFile->store('products', 'public');

        $galleryFile = UploadedFile::fake()->create('headphone_box.jpg', 100, 'image/jpeg');
        $galleryPath = $galleryFile->store('products', 'public');

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Tai Nghe Chống Ồn',
            'slug' => 'tai-nghe-chong-on',
            'price' => 2500000,
            'stock' => 10,
            'description' => 'Tai nghe chất âm đỉnh cao.',
            'main_image_url' => '/storage/'.$mainPath,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'url' => '/storage/'.$galleryPath,
            'sort_order' => 1,
        ]);

        Storage::disk('public')->assertExists($mainPath);
        Storage::disk('public')->assertExists($galleryPath);

        // Delete product
        $response = $this->actingAs($seller)->delete("/seller/products/{$product->id}");
        $response->assertRedirect(route('seller.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing($mainPath);
        Storage::disk('public')->assertMissing($galleryPath);
    }

    /**
     * Test buyer can submit review with uploaded photos.
     */
    public function test_buyer_can_submit_review_with_images(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $store = Store::create(['user_id' => User::factory()->create()->id, 'name' => 'Store 1', 'slug' => 'store-1']);
        $category = Category::create(['name' => 'Thời Trang', 'slug' => 'thoi-trang']);

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Áo Thun Cao Cấp',
            'slug' => 'ao-thun-cao-cap',
            'price' => 200000,
            'stock' => 50,
            'description' => 'Áo cotton thoáng mát.',
        ]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'status' => 'completed',
            'total' => 200000,
            'shipping_address' => ['recipient_name' => 'Buyer', 'phone' => '0901234567'],
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 200000,
            'subtotal' => 200000,
        ]);

        $reviewImg1 = UploadedFile::fake()->create('review_photo1.jpg', 100, 'image/jpeg');
        $reviewImg2 = UploadedFile::fake()->create('review_photo2.png', 100, 'image/png');

        $response = $this->actingAs($buyer)->post('/user/reviews', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Sản phẩm tuyệt vời, mặc rất vừa vặn và ưng ý!',
            'images' => [$reviewImg1, $reviewImg2],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'rating' => 5,
        ]);

        $review = $product->reviews()->first();
        $this->assertNotNull($review);
        $this->assertCount(2, $review->images);

        foreach ($review->images as $imgUrl) {
            $path = str_replace('/storage/', '', $imgUrl);
            Storage::disk('public')->assertExists($path);
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    protected Store $store;

    protected Category $parentCat;

    protected Category $childCat;

    protected function setUp(): void
    {
        parent::setUp();

        $seller = User::factory()->create();
        $this->store = Store::create([
            'user_id' => $seller->id,
            'name' => 'Apple Authorised Reseller',
            'slug' => 'apple-reseller',
            'status' => 'active',
            'is_mall' => true,
        ]);

        $this->parentCat = Category::create([
            'name' => 'Điện Thoại & Phụ Kiện',
            'slug' => 'dien-thoai-phu-kien',
        ]);

        $this->childCat = Category::create([
            'parent_id' => $this->parentCat->id,
            'name' => 'Điện Thoại Thông Minh',
            'slug' => 'dien-thoai-thong-minh',
        ]);
    }

    /**
     * Test live suggestions endpoint returns rich entities.
     */
    public function test_search_suggestions_returns_products_categories_brands_stores(): void
    {
        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Apple iPhone 15 Pro Max',
            'slug' => 'apple-iphone-15-pro-max',
            'brand' => 'Apple',
            'price' => 30000000,
            'stock' => 15,
            'status' => 'active',
            'rating' => 4.9,
        ]);

        $response = $this->getJson('/api/search/suggestions?q=Apple');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'products',
                'categories',
                'brands',
                'stores',
                'total_products',
                'view_all_url',
            ])
            ->assertJsonFragment(['name' => 'Apple iPhone 15 Pro Max'])
            ->assertJsonFragment(['name' => 'Apple'])
            ->assertJsonFragment(['name' => 'Apple Authorised Reseller']);
    }

    /**
     * Test suggestions return empty arrays if query is shorter than 2 characters.
     */
    public function test_search_suggestions_too_short(): void
    {
        $response = $this->getJson('/api/search/suggestions?q=a');

        $response->assertStatus(200)
            ->assertJson([
                'products' => [],
                'categories' => [],
                'brands' => [],
                'stores' => [],
                'total_products' => 0,
            ]);
    }

    /**
     * Test search results page /search?q=...
     */
    public function test_search_results_page_returns_matching_products(): void
    {
        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Apple iPhone 15',
            'slug' => 'apple-iphone-15',
            'brand' => 'Apple',
            'price' => 22000000,
            'stock' => 5,
            'status' => 'active',
        ]);

        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Samsung Galaxy S24',
            'slug' => 'samsung-galaxy-s24',
            'brand' => 'Samsung',
            'price' => 20000000,
            'stock' => 8,
            'status' => 'active',
        ]);

        $response = $this->get('/search?q=iPhone');

        $response->assertStatus(200)
            ->assertSee('Apple iPhone 15')
            ->assertDontSee('Samsung Galaxy S24');
    }

    /**
     * Test search empty state when no products match.
     */
    public function test_search_no_results_displays_empty_state(): void
    {
        $response = $this->get('/search?q=NonExistentProductXYZ');

        $response->assertStatus(200)
            ->assertSee('Không tìm thấy sản phẩm phù hợp');
    }

    /**
     * Test category listing route including products in child subcategories.
     */
    public function test_category_listing_page_includes_child_categories(): void
    {
        $childProduct = Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Xiaomi 14 Ultra',
            'slug' => 'xiaomi-14-ultra',
            'brand' => 'Xiaomi',
            'price' => 25000000,
            'stock' => 10,
            'status' => 'active',
        ]);

        // Request parent category
        $response = $this->get('/category/'.$this->parentCat->slug);

        $response->assertStatus(200)
            ->assertSee('Điện Thoại & Phụ Kiện')
            ->assertSee('Xiaomi 14 Ultra');
    }

    /**
     * Test brand listing page /brand/{brand}.
     */
    public function test_brand_listing_page_displays_brand_products(): void
    {
        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Sony WH-1000XM5',
            'slug' => 'sony-wh-1000xm5',
            'brand' => 'Sony',
            'price' => 8500000,
            'stock' => 20,
            'status' => 'active',
        ]);

        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Apple AirPods Max',
            'slug' => 'apple-airpods-max',
            'brand' => 'Apple',
            'price' => 13000000,
            'stock' => 5,
            'status' => 'active',
        ]);

        $response = $this->get('/brand/Sony');

        $response->assertStatus(200)
            ->assertSee('Thương hiệu: Sony')
            ->assertSee('Sony WH-1000XM5')
            ->assertDontSee('Apple AirPods Max');
    }

    /**
     * Test price filter (min_price and max_price).
     */
    public function test_filter_by_price_range(): void
    {
        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Cheap Cable',
            'slug' => 'cheap-cable',
            'price' => 100000,
            'stock' => 50,
            'status' => 'active',
        ]);

        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Mid Range Charger',
            'slug' => 'mid-range-charger',
            'price' => 500000,
            'stock' => 30,
            'status' => 'active',
        ]);

        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Expensive Phone',
            'slug' => 'expensive-phone',
            'price' => 20000000,
            'stock' => 10,
            'status' => 'active',
        ]);

        $response = $this->get('/search?min_price=200000&max_price=1000000');

        $response->assertStatus(200)
            ->assertSee('Mid Range Charger')
            ->assertDontSee('Cheap Cable')
            ->assertDontSee('Expensive Phone');
    }

    /**
     * Test rating filter.
     */
    public function test_filter_by_rating(): void
    {
        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Highly Rated Item',
            'slug' => 'highly-rated-item',
            'price' => 500000,
            'stock' => 10,
            'status' => 'active',
            'rating' => 4.8,
        ]);

        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Low Rated Item',
            'slug' => 'low-rated-item',
            'price' => 500000,
            'stock' => 10,
            'status' => 'active',
            'rating' => 2.5,
        ]);

        $response = $this->get('/search?rating=4');

        $response->assertStatus(200)
            ->assertSee('Highly Rated Item')
            ->assertDontSee('Low Rated Item');
    }

    /**
     * Test availability and mall filters.
     */
    public function test_filter_by_availability_and_mall(): void
    {
        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'In Stock Mall Product',
            'slug' => 'in-stock-mall-product',
            'price' => 500000,
            'stock' => 10,
            'is_mall' => true,
            'status' => 'active',
        ]);

        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Out Of Stock Product',
            'slug' => 'out-of-stock-product',
            'price' => 500000,
            'stock' => 0,
            'is_mall' => true,
            'status' => 'active',
        ]);

        $response = $this->get('/search?in_stock=1&is_mall=1');

        $response->assertStatus(200)
            ->assertSee('In Stock Mall Product')
            ->assertDontSee('Out Of Stock Product');
    }

    /**
     * Test multi-filter combination: search + brand + price + rating.
     */
    public function test_combined_filters(): void
    {
        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Apple iPad Pro M4',
            'slug' => 'apple-ipad-pro-m4',
            'brand' => 'Apple',
            'price' => 28000000,
            'stock' => 10,
            'rating' => 4.9,
            'status' => 'active',
        ]);

        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Apple iPad 9th Gen',
            'slug' => 'apple-ipad-9th-gen',
            'brand' => 'Apple',
            'price' => 7500000,
            'stock' => 10,
            'rating' => 4.2,
            'status' => 'active',
        ]);

        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Samsung Galaxy Tab S9',
            'slug' => 'samsung-galaxy-tab-s9',
            'brand' => 'Samsung',
            'price' => 22000000,
            'stock' => 10,
            'rating' => 4.8,
            'status' => 'active',
        ]);

        $response = $this->get('/search?q=iPad&brand=Apple&min_price=20000000&rating=4.5');

        $response->assertStatus(200)
            ->assertSee('Apple iPad Pro M4')
            ->assertDontSee('Apple iPad 9th Gen')
            ->assertDontSee('Samsung Galaxy Tab S9');
    }

    /**
     * Test sort options: price_asc, price_desc.
     */
    public function test_sorting_options(): void
    {
        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Product Cheap',
            'slug' => 'product-cheap',
            'price' => 100000,
            'stock' => 10,
            'status' => 'active',
        ]);

        Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->childCat->id,
            'name' => 'Product Expensive',
            'slug' => 'product-expensive',
            'price' => 900000,
            'stock' => 10,
            'status' => 'active',
        ]);

        $responseAsc = $this->get('/search?sort=price_asc');
        $responseAsc->assertStatus(200);
        $contentAsc = $responseAsc->getContent();
        $this->assertTrue(
            strpos($contentAsc, 'Product Cheap') < strpos($contentAsc, 'Product Expensive')
        );

        $responseDesc = $this->get('/search?sort=price_desc');
        $responseDesc->assertStatus(200);
        $contentDesc = $responseDesc->getContent();
        $this->assertTrue(
            strpos($contentDesc, 'Product Expensive') < strpos($contentDesc, 'Product Cheap')
        );
    }

    /**
     * Test pagination links preserve filter query string.
     */
    public function test_pagination_preserves_query_string(): void
    {
        // Create 20 products to trigger pagination (perPage = 16)
        for ($i = 1; $i <= 20; $i++) {
            Product::create([
                'store_id' => $this->store->id,
                'category_id' => $this->childCat->id,
                'name' => "Batch Product {$i}",
                'slug' => "batch-product-{$i}",
                'brand' => 'BatchBrand',
                'price' => 100000 * $i,
                'stock' => 10,
                'status' => 'active',
            ]);
        }

        $response = $this->get('/search?brand=BatchBrand&page=1');
        $response->assertStatus(200);
        // The link to page 2 must contain the brand parameter
        $response->assertSee('brand=BatchBrand');
    }

    /**
     * Test public guests can access search, category, and brand listing pages.
     */
    public function test_guest_can_access_all_discovery_routes(): void
    {
        $this->get('/search')->assertStatus(200);
        $this->get('/category/'.$this->parentCat->slug)->assertStatus(200);
        $this->get('/brand/Apple')->assertStatus(200);
        $this->get('/store/'.$this->store->slug)->assertStatus(200);
    }
}

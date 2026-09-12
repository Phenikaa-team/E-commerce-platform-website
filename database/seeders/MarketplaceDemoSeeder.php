<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MarketplaceDemoSeeder extends Seeder
{
    /**
     * Run the comprehensive demo marketplace seeders.
     */
    public function run(): void
    {
        // 1. Ensure Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'ShopMart Administrator',
                'username' => 'admin_shopmart',
                'phone' => '+84 901 000 999',
                'role' => 'admin',
                'status' => 'active',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=160&q=80',
            ]
        );
        $admin->update([
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // 2. Ensure Primary Seller User (Samsung Flagship Store)
        $seller = User::firstOrCreate(
            ['email' => 'seller@gmail.com'],
            [
                'name' => 'Samsung Electronics VN',
                'username' => 'samsung_seller',
                'phone' => '+84 912 345 678',
                'role' => 'seller',
                'status' => 'active',
                'avatar_url' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=200&q=80',
            ]
        );
        $seller->update([
            'password' => Hash::make('seller'),
            'role' => 'seller',
            'status' => 'active',
        ]);

        // Assign Samsung Flagship Store to this seller
        $samsungStore = Store::firstOrCreate(
            ['slug' => 'samsung-flagship-store'],
            [
                'user_id' => $seller->id,
                'name' => 'Samsung Flagship Store',
                'description' => 'Cửa hàng chính hãng Samsung - Điện thoại, máy tính bảng, phụ kiện, tivi và gia dụng thông minh.',
                'rating' => 4.9,
                'response_rate' => '98%',
                'followers' => '2.458',
                'is_mall' => true,
                'status' => 'active',
                'online_status' => 'Đang hoạt động',
                'phone' => '+84 912 345 678',
                'address' => '123 Nguyễn Văn Cừ, Quận 1, TP. Hồ Chí Minh',
                'banner_url' => asset('images/placeholders/store-banner-placeholder.svg'),
                'logo_url' => asset('images/placeholders/store-logo-placeholder.svg'),
                'bank_name' => 'Vietcombank',
                'bank_account_number' => '0071001234567',
                'bank_account_name' => 'CONG TY TNHH SAMSUNG ELECTRONICS VN',
            ]
        );
        $samsungStore->update([
            'user_id' => $seller->id,
            'name' => 'Samsung Flagship Store',
            'description' => 'Cửa hàng chính hãng Samsung - Điện thoại, máy tính bảng, phụ kiện, tivi và gia dụng thông minh.',
            'phone' => '+84 912 345 678',
            'address' => '123 Nguyễn Văn Cừ, Quận 1, TP. Hồ Chí Minh',
            'status' => 'active',
            'is_mall' => true,
            'rating' => 4.9,
            'followers' => '2.458',
            'response_rate' => '98%',
            'banner_url' => asset('images/placeholders/store-banner-placeholder.svg'),
            'logo_url' => asset('images/placeholders/store-logo-placeholder.svg'),
        ]);

        // 3. Populate Rich Buyers (matching user mockup screenshots)
        $buyersData = [
            [
                'name' => 'Nguyễn Văn A',
                'email' => 'nguyenvana@gmail.com',
                'username' => 'nguyenvana123',
                'phone' => '+84 912 345 678',
                'gender' => 'Nam',
                'birthday' => '15/08/2000',
                'membership_tier' => 'Khách hàng thân thiết',
                'coins' => 1248,
                'status' => 'active',
                'avatar_url' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=160&q=80',
                'address' => 'Số 123 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
            ],
            [
                'name' => 'Trần Thị B',
                'email' => 'tranthib@gmail.com',
                'username' => 'tranthib456',
                'phone' => '+84 938 111 222',
                'gender' => 'Nữ',
                'birthday' => '20/10/1998',
                'membership_tier' => 'Thành viên Vàng',
                'coins' => 850,
                'status' => 'active',
                'avatar_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=160&q=80',
                'address' => '45 Lê Lợi, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh',
            ],
            [
                'name' => 'Lê Văn C',
                'email' => 'levanc@gmail.com',
                'username' => 'levanc789',
                'phone' => '+84 909 333 444',
                'gender' => 'Nam',
                'birthday' => '05/03/1995',
                'membership_tier' => 'Thành viên Bạc',
                'coins' => 420,
                'status' => 'active',
                'avatar_url' => 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=160&q=80',
                'address' => '78 Hai Bà Trưng, Phường Đa Kao, Quận 1, TP. Hồ Chí Minh',
            ],
            [
                'name' => 'Phạm Thị D',
                'email' => 'phamthid@gmail.com',
                'username' => 'phamthid012',
                'phone' => '+84 977 555 666',
                'gender' => 'Nữ',
                'birthday' => '12/12/2001',
                'membership_tier' => 'Thành viên mới',
                'coins' => 50,
                'status' => 'banned',
                'avatar_url' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=160&q=80',
                'address' => '12 Nam Kỳ Khởi Nghĩa, Phường Võ Thị Sáu, Quận 3, TP. Hồ Chí Minh',
            ],
            [
                'name' => 'Hoàng Văn E',
                'email' => 'hoangvane@gmail.com',
                'username' => 'hoangvane345',
                'phone' => '+84 988 888 999',
                'gender' => 'Nam',
                'birthday' => '08/07/1997',
                'membership_tier' => 'Thành viên Bạc',
                'coins' => 310,
                'status' => 'active',
                'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=160&q=80',
                'address' => '234 Võ Văn Tần, Phường 5, Quận 3, TP. Hồ Chí Minh',
            ],
            [
                'name' => 'Đỗ Minh F',
                'email' => 'dominhf@gmail.com',
                'username' => 'dominhf678',
                'phone' => '+84 918 222 333',
                'gender' => 'Nam',
                'birthday' => '19/04/1993',
                'membership_tier' => 'Thành viên Vàng',
                'coins' => 1520,
                'status' => 'active',
                'avatar_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=160&q=80',
                'address' => '89 Cách Mạng Tháng 8, Phường 7, Quận 10, TP. Hồ Chí Minh',
            ],
            [
                'name' => 'Vũ Thị G',
                'email' => 'vuthig@gmail.com',
                'username' => 'vuthig901',
                'phone' => '+84 933 444 555',
                'gender' => 'Nữ',
                'birthday' => '28/02/1999',
                'membership_tier' => 'Khách hàng thân thiết',
                'coins' => 960,
                'status' => 'active',
                'avatar_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=160&q=80',
                'address' => '156 Nguyễn Đình Chiểu, Quận 3, TP. Hồ Chí Minh',
            ],
            [
                'name' => 'Lý Quốc H',
                'email' => 'lyquoch@gmail.com',
                'username' => 'lyquoch234',
                'phone' => '+84 908 666 777',
                'gender' => 'Nam',
                'birthday' => '14/11/1996',
                'membership_tier' => 'Thành viên mới',
                'coins' => 0,
                'status' => 'banned',
                'avatar_url' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=160&q=80',
                'address' => '210 Hoàng Văn Thụ, Phường 9, Quận Phú Nhuận, TP. Hồ Chí Minh',
            ],
            [
                'name' => 'Đặng Thị K',
                'email' => 'dangthik@gmail.com',
                'username' => 'dangthik567',
                'phone' => '+84 945 777 888',
                'gender' => 'Nữ',
                'birthday' => '01/09/2002',
                'membership_tier' => 'Thành viên Bạc',
                'coins' => 450,
                'status' => 'active',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=160&q=80',
                'address' => '34 Phan Xích Long, Phường 2, Quận Phú Nhuận, TP. Hồ Chí Minh',
            ],
            [
                'name' => 'Ngô Minh L',
                'email' => 'ngominhl@gmail.com',
                'username' => 'ngominhl890',
                'phone' => '+84 966 999 000',
                'gender' => 'Nam',
                'birthday' => '25/06/1994',
                'membership_tier' => 'Thành viên Kim Cương',
                'coins' => 3200,
                'status' => 'active',
                'avatar_url' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=160&q=80',
                'address' => '56 Trần Quang Khải, Phường Tân Định, Quận 1, TP. Hồ Chí Minh',
            ],
            [
                'name' => 'Bùi Thanh M',
                'email' => 'buithanhm@gmail.com',
                'username' => 'buithanhm123',
                'phone' => '+84 972 111 333',
                'gender' => 'Nam',
                'birthday' => '10/01/1998',
                'membership_tier' => 'Thành viên Bạc',
                'coins' => 280,
                'status' => 'active',
                'avatar_url' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=160&q=80',
                'address' => '92 Điện Biên Phủ, Phường 15, Quận Bình Thạnh, TP. Hồ Chí Minh',
            ],
            [
                'name' => 'Nguyễn Minh Anh',
                'email' => 'minhanh@gmail.com',
                'username' => 'minhanh_vn',
                'phone' => '+84 915 222 444',
                'gender' => 'Nữ',
                'birthday' => '17/05/2000',
                'membership_tier' => 'Thành viên Vàng',
                'coins' => 1100,
                'status' => 'active',
                'avatar_url' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=160&q=80',
                'address' => '180 Pasteur, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
            ],
        ];

        $createdBuyers = [];
        foreach ($buyersData as $bData) {
            $u = User::firstOrCreate(
                ['email' => $bData['email']],
                [
                    'name' => $bData['name'],
                    'username' => $bData['username'],
                    'phone' => $bData['phone'],
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
                    'status' => $bData['status'],
                    'gender' => $bData['gender'],
                    'birthday' => $bData['birthday'],
                    'membership_tier' => $bData['membership_tier'],
                    'coins' => $bData['coins'],
                    'avatar_url' => $bData['avatar_url'],
                ]
            );

            $u->update([
                'name' => $bData['name'],
                'username' => $bData['username'],
                'phone' => $bData['phone'],
                'role' => 'buyer',
                'status' => $bData['status'],
                'gender' => $bData['gender'],
                'birthday' => $bData['birthday'],
                'membership_tier' => $bData['membership_tier'],
                'coins' => $bData['coins'],
                'avatar_url' => $bData['avatar_url'],
            ]);

            // Ensure Default Address
            UserAddress::firstOrCreate(
                ['user_id' => $u->id, 'is_default' => true],
                [
                    'recipient_name' => $u->name,
                    'phone' => $u->phone,
                    'address_line' => $bData['address'],
                    'is_default' => true,
                ]
            );

            $createdBuyers[] = $u;
        }

        // 4. Populate Samsung Products
        $techCat = Category::where('name', 'like', '%Điện thoại%')->orWhere('slug', 'like', '%tech%')->first();
        if (! $techCat) {
            $techCat = Category::firstOrCreate(['slug' => 'dien-thoai-phu-kien'], ['name' => 'Điện thoại & Phụ kiện']);
        }

        $samsungProductsData = [
            [
                'name' => 'Samsung Galaxy S24 Ultra 256GB Chính Hãng',
                'price' => 31990000,
                'sale_price' => 28990000,
                'stock' => 45,
                'sold_count' => 148,
                'rating' => 4.9,
                'review_count' => 52,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=400&q=80',
                'description' => 'Siêu phẩm Galaxy S24 Ultra với chip Snapdragon 8 Gen 3 for Galaxy, khung viền Titan và cụm camera 200MP đột phá cùng quyền năng Galaxy AI.',
            ],
            [
                'name' => 'Samsung Galaxy Z Fold5 512GB - Màn hình gập đỉnh cao',
                'price' => 40990000,
                'sale_price' => 35990000,
                'stock' => 20,
                'sold_count' => 86,
                'rating' => 4.9,
                'review_count' => 34,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1580910051074-3eb694886505?auto=format&fit=crop&w=400&q=80',
                'description' => 'Màn hình mở rộng 7.6 inch Dynamic AMOLED 2X sắc nét, đa nhiệm linh hoạt, bản lề Flex tiên tiến gập không kẽ hở.',
            ],
            [
                'name' => 'Đồng hồ thông minh Samsung Galaxy Watch 6 40mm',
                'price' => 6990000,
                'sale_price' => 5490000,
                'stock' => 60,
                'sold_count' => 312,
                'rating' => 4.8,
                'review_count' => 120,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=400&q=80',
                'description' => 'Theo dõi giấc ngủ chuyên sâu, đo thành phần cơ thể BIA, điện tâm đồ ECG và nhịp tim chính xác suốt 24/7.',
            ],
            [
                'name' => 'Tai nghe không dây Samsung Galaxy Buds2 Pro',
                'price' => 4490000,
                'sale_price' => 3190000,
                'stock' => 85,
                'sold_count' => 420,
                'rating' => 4.9,
                'review_count' => 185,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=400&q=80',
                'description' => 'Âm thanh Hi-Fi 24bit chuẩn phòng thu, công nghệ chống ồn chủ động thông minh ANC và âm thanh vòm 360 độ.',
            ],
            [
                'name' => 'Máy tính bảng Samsung Galaxy Tab S9 FE WiFi 128GB',
                'price' => 9990000,
                'sale_price' => 8490000,
                'stock' => 30,
                'sold_count' => 95,
                'rating' => 4.8,
                'review_count' => 42,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?auto=format&fit=crop&w=400&q=80',
                'description' => 'Kèm bút S Pen kháng nước IP68, màn hình 90Hz mượt mà, hỗ trợ học tập và vẽ đồ họa chuyên nghiệp.',
            ],
            [
                'name' => 'Củ sạc siêu nhanh Samsung 45W Type-C Power Adapter',
                'price' => 890000,
                'sale_price' => 650000,
                'stock' => 150,
                'sold_count' => 890,
                'rating' => 4.9,
                'review_count' => 240,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&w=400&q=80',
                'description' => 'Sạc siêu nhanh Super Fast Charging 2.0 45W an toàn tuyệt đối, tương thích toàn bộ smartphone và tablet Samsung.',
            ],
            [
                'name' => 'Pin sạc dự phòng Samsung 20.000mAh 25W 3 cổng sạc',
                'price' => 1190000,
                'sale_price' => 890000,
                'stock' => 80,
                'sold_count' => 340,
                'rating' => 4.8,
                'review_count' => 92,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1609592426868-6c3848b9487c?auto=format&fit=crop&w=400&q=80',
                'description' => 'Dung lượng khủng 20.000mAh hỗ trợ sạc nhanh 25W 3 cổng cùng lúc, thiết kế thân thiện môi trường từ vật liệu tái chế.',
            ],
            [
                'name' => 'Màn hình thông minh Samsung M7 4K UHD 32 inch Smart Monitor',
                'price' => 9990000,
                'sale_price' => 7990000,
                'stock' => 15,
                'sold_count' => 64,
                'rating' => 4.9,
                'review_count' => 28,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=400&q=80',
                'description' => 'Tích hợp kho ứng dụng Smart TV, Netflix, YouTube không cần PC, hỗ trợ AirPlay và kết nối không dây Dex.',
            ],
        ];

        $createdProducts = [];
        foreach ($samsungProductsData as $pData) {
            $discountPercent = round((($pData['price'] - $pData['sale_price']) / $pData['price']) * 100);

            $prod = Product::firstOrCreate(
                ['slug' => Str::slug($pData['name'])],
                [
                    'store_id' => $samsungStore->id,
                    'category_id' => $techCat->id,
                    'name' => $pData['name'],
                    'original_price' => $pData['price'],
                    'price' => $pData['sale_price'],
                    'discount_percent' => $discountPercent,
                    'stock' => $pData['stock'],
                    'sold_count' => $pData['sold_count'],
                    'rating' => $pData['rating'],
                    'reviews_count' => $pData['review_count'],
                    'description' => $pData['description'],
                    'main_image_url' => $pData['thumbnail_url'],
                    'is_mall' => true,
                ]
            );

            $prod->update([
                'store_id' => $samsungStore->id,
                'original_price' => $pData['price'],
                'price' => $pData['sale_price'],
                'discount_percent' => $discountPercent,
                'stock' => $pData['stock'],
                'sold_count' => $pData['sold_count'],
                'rating' => $pData['rating'],
                'reviews_count' => $pData['review_count'],
                'main_image_url' => $pData['thumbnail_url'],
                'is_mall' => true,
            ]);

            $createdProducts[] = $prod;
        }

        // 5. Generate Real Orders & OrderItems for the past 30 days
        $statuses = ['completed', 'completed', 'completed', 'shipping', 'processing', 'pending', 'cancelled', 'refunded'];
        $ordersToCreate = 48; // Ensure great rich data across days

        for ($i = 0; $i < $ordersToCreate; $i++) {
            $buyer = $createdBuyers[$i % count($createdBuyers)];
            $prod = $createdProducts[$i % count($createdProducts)];

            // Distribute across last 28 days
            $daysAgo = ($i % 7) == 0 ? 0 : ($i % 28);
            $orderDate = Carbon::now()->subDays($daysAgo)->subHours(rand(1, 18))->subMinutes(rand(5, 50));

            $status = $statuses[$i % count($statuses)];
            $qty = rand(1, 2);
            $subtotal = $prod->price * $qty;
            $shippingFee = 30000;
            $discount = ($i % 3 === 0) ? 50000 : 0;
            $totalAmount = max(0, $subtotal + $shippingFee - $discount);

            $orderCode = 'SHM'.(24800 + $i);

            $order = Order::firstOrCreate(
                ['order_code' => $orderCode],
                [
                    'user_id' => $buyer->id,
                    'status' => $status,
                    'payment_method' => ($i % 2 === 0) ? 'cod' : 'banking',
                    'payment_status' => ($status === 'completed' || $status === 'shipping') ? 'paid' : 'pending',
                    'subtotal' => $subtotal,
                    'shipping_fee' => $shippingFee,
                    'discount_amount' => $discount,
                    'coupon_code' => $discount > 0 ? 'SAM50K' : null,
                    'total' => $totalAmount,
                    'shipping_address' => [
                        'name' => $buyer->name,
                        'phone' => $buyer->phone,
                        'address' => $buyer->addresses()->first()?->address_line ?? 'Số 123 Đường Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh',
                        'city' => 'TP. Hồ Chí Minh',
                    ],
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]
            );

            $order->update([
                'status' => $status,
                'subtotal' => $subtotal,
                'total' => $totalAmount,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Attach Order Item
            OrderItem::firstOrCreate(
                ['order_id' => $order->id, 'product_id' => $prod->id],
                [
                    'product_name' => $prod->name,
                    'unit_price' => $prod->price,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                    'selected_variant' => 'Bản tiêu chuẩn (Chính hãng)',
                ]
            );
        }

        // 6. Generate Real Customer Reviews in database
        $reviewComments = [
            [
                'rating' => 5,
                'comment' => 'Sản phẩm chất lượng, đóng gói cẩn thận, giao hàng nhanh!',
            ],
            [
                'rating' => 5,
                'comment' => 'Hàng chính hãng, rất ưng ý! Dùng cực kỳ mượt mà, camera xuất sắc.',
            ],
            [
                'rating' => 5,
                'comment' => 'Shop uy tín, tư vấn nhiệt tình, phục vụ chu đáo, cho shop 5 sao.',
            ],
            [
                'rating' => 4,
                'comment' => 'Sản phẩm tốt, nhưng shipper giao hàng hơi trễ một buổi.',
            ],
            [
                'rating' => 5,
                'comment' => 'Máy đẹp leng keng, pin trâu, bảo hành chính hãng kích hoạt ngay!',
            ],
            [
                'rating' => 5,
                'comment' => 'Chất lượng hoàn thiện đỉnh cao, âm thanh hay, rất hài lòng!',
            ],
        ];

        foreach ($createdProducts as $idx => $prod) {
            $revData = $reviewComments[$idx % count($reviewComments)];
            $revBuyer = $createdBuyers[$idx % count($createdBuyers)];

            Review::firstOrCreate(
                ['product_id' => $prod->id, 'user_id' => $revBuyer->id],
                [
                    'rating' => $revData['rating'],
                    'comment' => $revData['comment'],
                    'status' => 'approved',
                    'created_at' => Carbon::now()->subDays($idx * 2 + 1),
                ]
            );
        }

        // 7. Generate Real Store Coupons in database
        $storeCoupons = [
            [
                'code' => 'SAM50K',
                'name' => 'Giảm 50.000₫ đơn từ 500k',
                'description' => 'Áp dụng cho mọi phụ kiện và điện thoại Samsung Flagship',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'min_order_value' => 500000,
                'max_discount_amount' => 50000,
                'usage_limit' => 200,
                'used_count' => 35,
                'expires_at' => now()->addDays(45),
                'is_active' => true,
            ],
            [
                'code' => 'SAMSUNG10',
                'name' => 'Giảm 10% Siêu Tiệc Công Nghệ',
                'description' => 'Giảm tối đa 200.000₫ cho đơn hàng Samsung',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_order_value' => 1000000,
                'max_discount_amount' => 200000,
                'usage_limit' => 150,
                'used_count' => 24,
                'expires_at' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'code' => 'SAMZVIP',
                'name' => 'Voucher VIP 500K Dòng Cao Cấp',
                'description' => 'Giảm ngay 500.000₫ cho Galaxy S24 Ultra & Z Fold5',
                'discount_type' => 'fixed',
                'discount_value' => 500000,
                'min_order_value' => 15000000,
                'max_discount_amount' => 500000,
                'usage_limit' => 50,
                'used_count' => 18,
                'expires_at' => now()->addDays(60),
                'is_active' => true,
            ],
        ];

        foreach ($storeCoupons as $cData) {
            Coupon::updateOrCreate(
                ['code' => $cData['code']],
                array_merge($cData, ['store_id' => $samsungStore->id])
            );
        }
    }
}

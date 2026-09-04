# Marketplace API

Nền tảng REST API Laravel 13 cho website sàn thương mại điện tử, chạy PHP 8.4 và SQLite mặc định.

## Chạy nhanh

```powershell
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

API mặc định tại `http://127.0.0.1:8000/api/v1`.

## Endpoint chính

- `GET /api/v1/health` — kiểm tra dịch vụ
- `GET /api/v1/categories` — danh mục sản phẩm
- `GET /api/v1/stores` — danh sách cửa hàng
- `GET /api/v1/products?search=...&category_id=...` — tìm kiếm, phân trang sản phẩm
- `GET /api/v1/products/{id}` — chi tiết sản phẩm
- `POST /api/v1/cart/items` — thêm sản phẩm vào giỏ (`product_id`, `quantity`, tùy chọn `cart_id`)
- `GET /api/v1/cart/{id}` — xem giỏ hàng
- `POST /api/v1/orders` — tạo đơn (`cart_id`, `shipping_address`)
- `GET /api/v1/orders/{id}` — xem đơn hàng

## Cấu trúc nghiệp vụ

Các bảng marketplace gồm `stores`, `categories`, `products`, `carts`, `cart_items`, `orders` và `order_items`. Dữ liệu mẫu được tạo bằng `php artisan db:seed`.

Các phần tiếp theo nên bổ sung: Sanctum/JWT, phân quyền buyer/seller/admin, upload ảnh sản phẩm, thanh toán, vận chuyển và frontend (Vue/React).

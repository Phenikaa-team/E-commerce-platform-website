<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create(['name' => 'Marketplace Admin', 'email' => 'admin@example.com']);
        $store = Store::create(['user_id' => $user->id, 'name' => 'Demo Store', 'slug' => 'demo-store', 'description' => 'Cửa hàng mẫu']);
        $electronics = Category::create(['name' => 'Điện tử', 'slug' => 'dien-tu']);
        $fashion = Category::create(['name' => 'Thời trang', 'slug' => 'thoi-trang']);
        Product::create(['store_id'=>$store->id,'category_id'=>$electronics->id,'name'=>'Tai nghe không dây','slug'=>'tai-nghe-khong-day','description'=>'Âm thanh rõ, pin dùng cả ngày.','price'=>890000,'stock'=>50]);
        Product::create(['store_id'=>$store->id,'category_id'=>$fashion->id,'name'=>'Áo thun basic','slug'=>'ao-thun-basic','description'=>'Cotton mềm mại, dễ phối đồ.','price'=>199000,'stock'=>100]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoUser = User::updateOrCreate(
            ['email' => 'example@gmail.com'],
            [
                'name' => 'Nguyễn Văn A',
                'username' => 'example',
                'phone' => '+84 912 345 678',
                'password' => Hash::make('123456'),
                'avatar_url' => 'https://images.unsplash.com/photo-1566492031773-4f4e44671857?auto=format&fit=crop&w=400&q=80',
                'cover_url' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=1200&q=80',
                'membership_tier' => 'Thành viên Bạc',
                'joined_date' => 'Tham gia từ 06/2024',
                'gender' => 'Chưa cập nhật',
                'birthday' => 'Chưa cập nhật',
                'coins' => 120,
                'voucher_count' => 3,
                'favorite_count' => 4,
                'order_count' => 12,
                'review_count' => 3,
            ]
        );

        $demoUser->addresses()->delete();
        $demoUser->addresses()->create([
            'recipient_name' => 'Nguyễn Văn A',
            'phone' => '(+84) 912 345 678',
            'address_line' => 'Số 123 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
            'is_default' => true,
        ]);
        $demoUser->addresses()->create([
            'recipient_name' => 'Nguyễn Văn A',
            'phone' => '(+84) 912 345 678',
            'address_line' => 'Số 456 Đường Lê Lợi, Phường Đống Đa, Quận Đống Đa, Hà Nội',
            'is_default' => false,
        ]);
    }
}

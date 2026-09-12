<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

#[Signature('db:backup')]
#[Description('Tạo bản sao lưu cơ sở dữ liệu và hiển thị thống kê bảng dữ liệu')]
class DatabaseBackupCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $connection = config('database.default');
        $this->info("Đang kiểm tra kết nối database: [{$connection}]...");

        $backupDir = database_path('backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = date('Y_m_d_His');

        if ($connection === 'sqlite') {
            $sourceFile = config('database.connections.sqlite.database');
            if (! File::exists($sourceFile)) {
                $this->error("Không tìm thấy file SQLite tại: {$sourceFile}");

                return Command::FAILURE;
            }

            $backupFile = $backupDir.DIRECTORY_SEPARATOR."backup_{$timestamp}.sqlite";
            File::copy($sourceFile, $backupFile);

            $sizeKb = round(filesize($backupFile) / 1024, 2);
            $this->info('Sao lưu thành công file SQLite!');
            $this->line("Đường dẫn: <comment>{$backupFile}</comment>");
            $this->line("Kích thước: <comment>{$sizeKb} KB</comment>");
        } else {
            $this->info("Cơ sở dữ liệu đang dùng: {$connection}.");
            $this->line('Để sao lưu MySQL, bạn có thể chạy: <comment>mysqldump -u root -p '.config('database.connections.mysql.database')." > database/backups/backup_{$timestamp}.sql</comment>");
        }

        // Display Table Statistics
        $this->newLine();
        $this->info('Thống Kê Dữ Liệu Hiện Tại Trong Hệ Thống:');

        $tables = [
            'users' => 'Tài khoản người dùng',
            'stores' => 'Gian hàng đối tác',
            'categories' => 'Danh mục ngành hàng',
            'products' => 'Sản phẩm niêm yết',
            'orders' => 'Đơn đặt hàng',
            'order_items' => 'Chi tiết sản phẩm trong đơn',
            'coupons' => 'Mã giảm giá (Vouchers)',
            'reviews' => 'Đánh giá & Bình luận',
            'user_addresses' => 'Sổ địa chỉ giao hàng',
            'carts' => 'Giỏ hàng hoạt động',
        ];

        $rows = [];
        foreach ($tables as $table => $label) {
            try {
                $count = DB::table($table)->count();
                $rows[] = [$table, $label, $count];
            } catch (\Throwable $e) {
                $rows[] = [$table, $label, 'N/A'];
            }
        }

        $this->table(['Tên Bảng', 'Mô Tả Chức Năng', 'Số Lượng Bản Ghi'], $rows);

        $this->newLine();

        return Command::SUCCESS;
    }
}

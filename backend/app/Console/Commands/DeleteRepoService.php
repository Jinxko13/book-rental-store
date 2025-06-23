<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeleteRepoService extends Command
{
    protected $signature = 'delete:repo+service {--model=}';
    protected $description = 'Xoá Repository & Service đã tạo theo model';

    public function handle()
    {
        $model = $this->option('model');

        if (!$model) {
            $this->error('❌ Vui lòng nhập --model=ModelName');
            return;
        }

        $files = [
            app_path("Repositories/Interface/{$model}RepositoryInterface.php"),
            app_path("Repositories/Eloquent/{$model}Repository.php"),
            app_path("Services/Interface/{$model}ServiceInterface.php"),
            app_path("Services/Impl/{$model}Service.php"),
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
                $this->info("🗑️ Đã xóa: $file");
            } else {
                $this->warn("⚠️ Không tìm thấy: $file");
            }
        }

        $this->info("✅ Đã xoá tất cả file liên quan đến model {$model}.");
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Tiểu thuyết', 'Trinh thám', 'Khoa học', 'Lịch sử',
                'Thiếu nhi', 'Tâm lý học', 'Kinh doanh', 'Công nghệ thông tin',
                'Ẩm thực', 'Văn học Việt Nam', 'Văn học nước ngoài'
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

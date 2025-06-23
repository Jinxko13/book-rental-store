<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DiscountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => Str::random(10),
            'description' => fake('vi_VN')->paragraph,
            'discount_percent' => fake()->randomFloat(2, 5, 50),
            'valid_from' => fake()->dateTimeBetween('-1 month', 'now'),
            'valid_to' => fake()->dateTimeBetween('now', '+1 month'),
        ];
    }
}

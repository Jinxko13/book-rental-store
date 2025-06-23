<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\Promotion;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        Discount::factory()->count(10)->create();
    }
}

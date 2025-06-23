<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RentalReturn;

class ReturnSeeder extends Seeder
{
    public function run(): void
    {
        RentalReturn::factory(10)->create();
    }
}

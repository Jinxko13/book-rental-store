<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Rental;

class RentalReturnFactory extends Factory
{
    public function definition(): array
    {
        $rental = Rental::inRandomOrder()->where('status', '!=', 'renting')->first();

        return [
            'rental_id' => $rental?->id,
            'return_date' => $this->faker->dateTimeBetween($rental?->rental_date ?? '-1 month', 'now'),
            'condition' => $this->faker->randomElement(['good', 'damaged', 'lost']),
            'penalty_fee' => 0,
            'late_fee' => 0,
            'refund_amount' => $this->faker->randomFloat(2, 0, 50000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

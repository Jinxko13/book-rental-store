<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class RentalFactory extends Factory
{
    public function definition(): array
    {
        $rentalDate = $this->faker->dateTimeBetween('-1 month', 'now');
        $dueDate = (clone $rentalDate)->modify('+7 days');

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'rental_date' => $rentalDate,
            'due_date' => $dueDate,
            'status' => $this->faker->randomElement(['renting', 'returned', 'overdue']),
            'deposit' => $this->faker->randomFloat(2, 10000, 100000),
            'rental_fee' => $this->faker->randomFloat(2, 5000, 50000),
            'paid' => $this->faker->randomFloat(2, 0, 0),
            'discount_id'=>$this->faker->numberBetween(1,10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

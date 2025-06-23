<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rental;
use App\Models\Book;
use App\Models\RentalDetail;

class RentalSeeder extends Seeder
{
    public function run(): void
    {
        Rental::factory(15)->create()->each(function ($rental) {
            $books = Book::inRandomOrder()->take(rand(1, 3))->get();

            foreach ($books as $book) {
                RentalDetail::create([
                    'rental_id' => $rental->id,
                    'book_id' => $book->id,
                    'quantity' => rand(1, 2),
                    'book_price' => $book->price,
                ]);
            }
        });
    }
}

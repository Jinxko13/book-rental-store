<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;
use App\Models\BookImage;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::pluck('id')->toArray();
        
        Book::factory(20)->create()->each(function ($book) use ($categoryIds) {
            $book->categories()->attach(
                collect($categoryIds)->random(rand(1, 3))->all()
            );
            $sampleImages = [
                "https://covers.openlibrary.org/b/id/10521256-L.jpg",
                "https://covers.openlibrary.org/b/id/11153220-L.jpg",
                "https://covers.openlibrary.org/b/id/10958333-L.jpg",
                "https://covers.openlibrary.org/b/id/10909258-L.jpg",
                "https://covers.openlibrary.org/b/id/10832566-L.jpg",
                "https://covers.openlibrary.org/b/id/10830035-L.jpg",
                "https://covers.openlibrary.org/b/id/10421898-L.jpg",
                "https://covers.openlibrary.org/b/id/10662291-L.jpg",
                "https://covers.openlibrary.org/b/id/10953908-L.jpg",
                "https://covers.openlibrary.org/b/id/11153701-L.jpg",
                "https://covers.openlibrary.org/b/id/11153284-L.jpg",
                "https://covers.openlibrary.org/b/id/10612356-L.jpg",
                "https://covers.openlibrary.org/b/id/11033254-L.jpg",
                "https://covers.openlibrary.org/b/id/11151117-L.jpg",
                "https://covers.openlibrary.org/b/id/10730292-L.jpg",
                "https://covers.openlibrary.org/b/id/11125556-L.jpg",
                "https://covers.openlibrary.org/b/id/10202187-L.jpg",
                "https://covers.openlibrary.org/b/id/11153217-L.jpg",
                "https://covers.openlibrary.org/b/id/11032209-L.jpg",
                "https://covers.openlibrary.org/b/id/10468792-L.jpg",
            ];
            // Gán ảnh cho sách (1–3 ảnh)
            $images = collect($sampleImages)->random(rand(1, 3))->all();
            foreach ($images as $imageUrl) {
                BookImage::create([
                    'book_id' => $book->id,
                    'image_url' => $imageUrl,
                ]);
            }
        });
    }
}

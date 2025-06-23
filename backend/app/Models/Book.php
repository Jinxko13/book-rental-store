<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'author_id', 'quantity', 'price', 'description'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category');
    }

    public function rentalDetails()
    {
        return $this->hasMany(RentalDetail::class);
    }
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function BookImage():HasMany
    {
        return $this->hasMany(BookImage::class);
    }
}

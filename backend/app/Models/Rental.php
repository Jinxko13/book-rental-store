<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'rental_date', 'due_date', 'status', 'deposit', 'rental_fee', 'paid', 'discount_id',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Model Rental.php
    public function books()
    {
        return $this->belongsToMany(Book::class, 'rental_details');  // 'rental_details' là bảng trung gian
    }

    public function discounts(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function rentalDetails():HasMany
    {
        return $this->hasMany(RentalDetail::class);
    }

    public function returns():HasOne
    {
        return $this->hasOne(RentalReturn::class);
    }
}

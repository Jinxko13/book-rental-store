<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalReturn extends Model
{
    use HasFactory;

    protected $table = 'returns';
    protected $fillable = [
    'rental_id', 'return_date', 'condition', 'refund_amount','penalty_fee','late_fee'
];

    public function rental()
{
    return $this->belongsTo(Rental::class);
}
}

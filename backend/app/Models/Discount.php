<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Discount extends Model implements Transformable
{
    use TransformableTrait;
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'discount_percent', 'valid_from', 'valid_to',
    ];

    public function rentals():hasMany
    {
        return $this->hasMany(Rental::class);
    }
}

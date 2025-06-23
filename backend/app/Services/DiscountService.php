<?php

namespace App\Services;

use App\Models\Discount;
use App\Repositories\Interfaces\DiscountRepository;
use Illuminate\Database\Eloquent\Collection;

class DiscountService
{
    private $discountRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(DiscountRepository $discountRepository)
    {
        $this->discountRepository = $discountRepository;
    }

    public function all() : Collection
    {
        return $this->discountRepository->all();
    }

    public function show(string $id) : Discount
    {
        return $this->discountRepository->find($id);
    }

    public function create(array $data) : Discount
    {
        return $this->discountRepository->create($data);
    }

    public function update(array $data, $id) : Discount
    {
        return $this->discountRepository->update($data, $id);
    }

    public function delete($id) : bool
    {
        return $this->discountRepository->delete($id);
    }

    public function apply($id) : float
    {
        return $this->discountRepository->find($id)->discount_percent;
    }

    public function findByName(string $name)
    {
        return $this->discountRepository->where('name', $name)->first();
    }
}


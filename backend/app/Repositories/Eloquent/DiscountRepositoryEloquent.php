<?php

namespace App\Repositories\Eloquent;

use App\Models\Discount;
use App\Repositories\Interfaces\DiscountRepository;
//use App\Validators\DiscountValidator;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class DiscountRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DiscountRepositoryEloquent extends BaseRepository implements DiscountRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Discount::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}

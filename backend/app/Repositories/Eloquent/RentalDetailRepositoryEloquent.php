<?php

namespace App\Repositories\Eloquent;

use App\Models\RentalDetail;
use App\Repositories\Interfaces\RentalDetailRepository;
use App\Validators\RentalDetailValidator;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class RentalDetailRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class RentalDetailRepositoryEloquent extends BaseRepository implements RentalDetailRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return RentalDetail::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}

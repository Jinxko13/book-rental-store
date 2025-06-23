<?php

namespace App\Repositories\Eloquent;

use App\Models\RentalReturn;
use App\Repositories\Interfaces\BookReturnRepository;
use App\Validators\BookReturnValidator;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class BookReturnRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BookReturnRepositoryEloquent extends BaseRepository implements BookReturnRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return RentalReturn::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}

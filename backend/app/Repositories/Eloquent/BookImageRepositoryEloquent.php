<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\BookImageRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

//use App\Validators\BookImageValidator;

/**
 * Class BookImageRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BookImageRepositoryEloquent extends BaseRepository implements BookImageRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return \App\Models\BookImage::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}

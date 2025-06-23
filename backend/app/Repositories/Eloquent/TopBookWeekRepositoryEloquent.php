<?php

namespace App\Repositories\Eloquent;

use App\Models\TopBookWeek;
use App\Repositories\Interfaces\TopBookWeekRepositoryInterface;
use Carbon\Carbon;

class TopBookWeekRepositoryEloquent implements TopBookWeekRepositoryInterface
{
    protected $model;

    public function __construct(TopBookWeek $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function fetchTopBooksByDateRange(Carbon $start, Carbon $end)
    {
        return $this->model
            ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->orderByDesc('total_rented')
            ->get();
    }
}

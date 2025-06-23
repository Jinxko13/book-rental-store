<?php
    namespace App\Repositories\Interfaces;

    use Carbon\Carbon;
    
    interface TopBookWeekRepositoryInterface
    {
        public function fetchTopBooksByDateRange(Carbon $start, Carbon $end);
    }
    
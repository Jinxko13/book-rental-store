<?php
    namespace App\Services;

    use App\Repositories\Interfaces\TopBookWeekRepositoryInterface;
    use Carbon\Carbon;
    
    class TopBookWeekService
    {
        protected $repo;
    
        public function __construct(TopBookWeekRepositoryInterface $repo)
        {
            $this->repo = $repo;
        }
    
        public function getRecentTopBooks()
        {
            $start = Carbon::now()->subDays(6)->startOfDay();
            $end = Carbon::now()->endOfDay();
    
            $books = $this->repo->fetchTopBooksByDateRange($start, $end);
    
            return [
                'range' => [$start->toDateString(), $end->toDateString()],
                'data' => $books,
            ];
        }
    }
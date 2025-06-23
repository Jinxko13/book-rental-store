<?php
    namespace App\Repositories\Eloquent;

    use App\Models\MonthlyTopBook;
    use App\Repositories\Interfaces\MonthlyTopBookRepositoryInterface;
    
    class MonthlyTopBookRepositoryEloquent implements MonthlyTopBookRepositoryInterface
    {
        public function deleteByMonth(int $year, int $month): void
        {
            MonthlyTopBook::where('year', $year)->where('month', $month)->delete();
        }
    
        public function bulkInsert(array $data): void
        {
            MonthlyTopBook::insert($data);
        }
    }
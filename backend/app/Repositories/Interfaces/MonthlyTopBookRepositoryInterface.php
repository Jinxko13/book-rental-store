<?php
    namespace App\Repositories\Interfaces;

    interface MonthlyTopBookRepositoryInterface
    {
        public function deleteByMonth(int $year, int $month): void;
        public function bulkInsert(array $data): void;
    }
    
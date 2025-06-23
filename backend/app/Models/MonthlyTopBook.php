<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyTopBook extends Model
{
    public function deleteByMonth($year, $month)
    {
        MonthlyTopBook::where('year', $year)->where('month', $month)->delete();
    }

    public function bulkInsert(array $data)
    {
        MonthlyTopBook::insert($data);
    }
}
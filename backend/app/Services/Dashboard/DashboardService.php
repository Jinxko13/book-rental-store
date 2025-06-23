<?php

namespace App\Services\Dashboard;


use App\Models\Rental;
use App\Models\User;
use App\Enums\RangeEnum;
use App\Models\Book;
use App\Models\RentalReturn;
use App\Repositories\Interfaces\BookRepository;
use App\Repositories\Interfaces\RentalRepository;
use App\Repositories\Interfaces\UserRepository;
use MongoDB\Driver\Query;

class DashboardService
{
    protected $bookRepository;
    protected $rentalRepository;
    protected $userRepository;

    public function __construct(BookRepository $bookRepository, RentalRepository $rentalRepository, UserRepository $userRepository)
    {
        $this->bookRepository = $bookRepository;
        $this->rentalRepository = $rentalRepository;
        $this->userRepository = $userRepository;
    }

    public function getBookDashboard()
    {
        $totalBook = $this->bookRepository->sum('quantity');
        $numberOfRental = $this->rentalRepository
        ->whereIn("status", ["overdue", "renting"])
        ->join('rental_details', 'rentals.id', '=', 'rental_details.rental_id')
        ->sum('rental_details.quantity');
        return [
            "totalBook" => $totalBook,
            "numberOfRental" => $numberOfRental,
        ];

    }

    private function getRevnueDashboard($previousDateStart, $previousDateEnd)
    {
        $totalRevenue = 0;
        $rentalReturnList = RentalReturn::query()->get();
        foreach ($rentalReturnList as $rentalReturn) {
            $totalPrice = $rentalReturn->rental_fee + $rentalReturn->rental->deposit;
            $totalRevenue += $totalPrice - $rentalReturn->refund_amount;
        }


        $previousRentalReturns = RentalReturn::whereBetween('created_at', [$previousDateStart, $previousDateEnd])->get();
        $previousRevenue = 0;
        if ($previousRentalReturns->count() > 0) {
            foreach ($previousRentalReturns as $rentalReturn) {
                $totalPrice = $rentalReturn->rental_fee + $rentalReturn->rental->deposit;
                $previousRevenue += $totalPrice - $rentalReturn->refund_amount;
            }
        }

        $percentIncrease = 0;
        if ($previousRevenue > 0) {
            $percentIncrease = (($totalRevenue - $previousRevenue) / $previousRevenue) * 100;
        }
        return [
            "totalRevenue" => $totalRevenue,
            "percentIncrease" => $percentIncrease,
        ];
    }

    private function getUserDashboard($previousDateStart, $previousDateEnd)
    {
        $totalUser = User::query()->get()->count();
        $previousUser = User::query()->whereBetween('created_at', [$previousDateStart, $previousDateEnd])->get()->count();
        $numberIncrease = $totalUser - $previousUser;
        return [
            "totalUser" => $totalUser,
            "numberIncrease" => $numberIncrease,
        ];
    }

    private function getRentalDashboard($previousDateStart, $previousDateEnd, $range)
    {
        $currentDateStart = now()->startOfMonth();
        $currentDateEnd = now()->endOfMonth();
        if ($range == RangeEnum::WEEK) {
            $currentDateStart = now()->startOfWeek();
            $currentDateEnd = now()->endOfWeek();
        }
        $currentRentalCount = Rental::query()
            ->whereBetween('created_at', [$currentDateStart, $currentDateEnd])
            ->count();

        $previousRentalCount = Rental::whereBetween('created_at', [$previousDateStart, $previousDateEnd])->count();

        $percentChange = 0;
        if ($previousRentalCount > 0) {
            $percentChange = (($currentRentalCount - $previousRentalCount) / $previousRentalCount) * 100;
        }

        return [
            "total" => $currentRentalCount,
            "increase" => $percentChange,
        ];
    }


    public function dashboard($range)
    {


        $previousDateStart = now();
        $previousDateEnd = now();

        if ($range == RangeEnum::MONTH) {
            $previousDateStart = $previousDateStart->subMonth()->startOfMonth();
            $previousDateEnd = $previousDateEnd->subMonth()->endOfMonth();
        }

        if ($range == RangeEnum::WEEK) {
            $previousDateStart = $previousDateStart->subWeek()->startOfWeek();
            $previousDateEnd = $previousDateEnd->subWeek()->endOfWeek();
        }

        $bookDashboard = $this->getBookDashboard();
        $revenueDashboard = $this->getRevnueDashboard($previousDateStart, $previousDateEnd);
        $userDashboard = $this->getUserDashboard($previousDateStart, $previousDateEnd);
        $rentalDashboard = $this->getRentalDashboard($previousDateStart, $previousDateEnd, $range);

        return [
            "book" => $bookDashboard,
            "revenue" => [
                "revenueNow" => $revenueDashboard["totalRevenue"],
                "increase" => $revenueDashboard["percentIncrease"],
            ],
            "user" => [
                "total" => $userDashboard["totalUser"],
                "increase" => $userDashboard["numberIncrease"],
            ],
            "rental" => $rentalDashboard,
        ];
    }


    public function getRevenueForChart($range)
    {
        $now = now();
        $labels = [];
        $current = [];
        $previous = [];

        if ($range == RangeEnum::WEEK) {
            $startOfWeek = $now->copy()->startOfWeek();
            $labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

            for ($i = 0; $i < 7; $i++) {
                $date = $startOfWeek->copy()->addDays($i)->toDateString();

                $current[] = $this->calculateRevenueByDate($date);
                $previousDate = $startOfWeek->copy()->subWeek()->addDays($i)->toDateString();
                $previous[] = $this->calculateRevenueByDate($previousDate);
            }

        } elseif ($range == RangeEnum::MONTH) {
            for ($i = 1; $i <= 12; $i++) {
                $labels[] = "T{$i}";
                $current[] = $this->calculateRevenueByMonth($now->year, $i);
                $previous[] = $this->calculateRevenueByMonth($now->year - 1, $i);
            }

        } elseif ($range == RangeEnum::QUARTER) {
            $labels = ['Q1', 'Q2', 'Q3', 'Q4'];
            for ($q = 1; $q <= 4; $q++) {
                $current[] = $this->calculateRevenueByQuarter($now->year, $q);
                $previous[] = $this->calculateRevenueByQuarter($now->year - 1, $q);
            }

        } elseif ($range == RangeEnum::YEAR) {
            $startYear = $now->year - 5;
            $labels = [];
            for ($y = $startYear; $y <= $now->year; $y++) {
                $labels[] = (string)$y;
                $current[] = $this->calculateRevenueByYear($y);
            }
        }

        $result = [
            'labels' => $labels,
            'current' => $current,
        ];

        if ($range !== RangeEnum::YEAR) {
            $result['previous'] = $previous;
        }
        return $result;
    }

    private function calculateRevenueByDate($date)
    {
        $returns = RentalReturn::whereDate('created_at', $date)->get();
        $revenue = 0;

        foreach ($returns as $rentalReturn) {
            $revenue += ($rentalReturn->rental_fee + $rentalReturn->rental->deposit) - $rentalReturn->refund_amount;
        }

        return $revenue;
    }

    private function calculateRevenueByMonth($year, $month)
    {
        $returns = RentalReturn::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        $revenue = 0;
        foreach ($returns as $rentalReturn) {
            $revenue += ($rentalReturn->rental_fee + $rentalReturn->rental->deposit) - $rentalReturn->refund_amount;
        }

        return $revenue;
    }

    private function calculateRevenueByQuarter($year, $quarter)
    {
        $startMonth = ($quarter - 1) * 3 + 1;
        $start = now()->setYear($year)->setMonth($startMonth)->startOfMonth();
        $end = (clone $start)->addMonths(2)->endOfMonth();

        $returns = RentalReturn::whereBetween('created_at', [$start, $end])->get();

        $revenue = 0;
        foreach ($returns as $rentalReturn) {
            $revenue += ($rentalReturn->rental_fee + $rentalReturn->rental->deposit) - $rentalReturn->refund_amount;
        }

        return $revenue;
    }

    private function calculateRevenueByYear($year)
    {
        $returns = RentalReturn::whereYear('created_at', $year)->get();

        $revenue = 0;
        foreach ($returns as $rentalReturn) {
            $revenue += ($rentalReturn->rental_fee + $rentalReturn->rental->deposit) - $rentalReturn->refund_amount;
        }

        return $revenue;
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\RangeEnum;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private  $dashboardService;
    public function __construct(DashboardService $dashboardService){
        $this->dashboardService = $dashboardService;
    }

    public function getDashboard(Request $request){
        $range = $request->query('range');
        try {
            $Resp=$this->dashboardService->dashboard($range);
            return response()->json([
                'data' => $Resp
            ],200);
        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
            ]);
        }

    }
    public function getDashboardCharData(Request $request){
        $rangeStr = $request->query('range');
        try{
            $range = RangeEnum::from($rangeStr);
            $resp=$this->dashboardService->getRevenueForChart($range);
            return response()->json([
                'data' => $resp
            ],200);
        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'code' => $exception->getCode(),
            ]);
        }
    }

}

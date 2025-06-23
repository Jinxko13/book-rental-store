<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ActivityLogService;

class ActivityLogController extends Controller
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * Hiển thị danh sách log
     */
    public function index(Request $request)
    {
        $filters = [
            'from_date'      => $request->input('from_date'),
            'to_date'        => $request->input('to_date'),
            'user_id'        => $request->input('user_id'),
            'action'         => $request->input('action'),
            'ip_address'     => $request->input('ip_address'),
            'computer_name'  => $request->input('computer_name'),
        ];

        $logs = $this->activityLogService->getAll($filters);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }
}
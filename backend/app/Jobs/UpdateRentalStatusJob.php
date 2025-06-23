<?php

namespace App\Jobs;

use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateRentalStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        // Không cần tham số nào cho job này
    }

    public function handle()
    {
        $today = Carbon::now()->setTimezone('Asia/Ho_Chi_Minh')->toDateString();
        Log::info("Ngày hôm nay: " . $today);

        // Lấy tất cả các đơn thuê đang ở trạng thái "renting" và có ngày trả sách đã qua
        $rentals = Rental::where('status', 'renting')
            ->whereDate('due_date', '<', $today)
            ->get();

        foreach ($rentals as $rental) {
            // Cập nhật trạng thái thành "overdue" nếu đã quá hạn
            $rental->status = 'overdue';
            $rental->save();

            Log::info("Đơn thuê ID: {$rental->id} đã được cập nhật thành trạng thái 'overdue'");
        }
    }
}

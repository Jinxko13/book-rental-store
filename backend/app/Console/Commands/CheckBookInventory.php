<?php

namespace App\Console\Commands;

use App\Mail\LowBookInventoryNotification;
use App\Models\Book;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckBookInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:check-books';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra số lượng sách tồn kho và gửi thông báo nếu dưới mức tối thiểu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minQuantity = config('inventory.min_book_quantity');
        $lowStockBooks = Book::where('quantity', '<', $minQuantity)->get();

        if ($lowStockBooks->isEmpty()) {
            $this->info('Tất cả sách đều có số lượng trên mức tối thiểu (' . $minQuantity . ')');
        } else {
            $this->info($lowStockBooks->count() . ' sách có số lượng dưới mức tối thiểu (' . $minQuantity . ')');

            $recipientMail = collect();
            if (config('inventory.staff_notifications')) {
                $staffEmail = User::where('role', 'staff')->get()->pluck('email')->toArray();
                $recipientMail = $recipientMail->merge($staffEmail);
            }

            // Thêm các email bổ sung từ config nếu có
            $additionalEmails = config('inventory.additional_emails', []);
            if (!empty($additionalEmails)) {
                $recipientMail = $recipientMail->merge($additionalEmails);
            }

            if (config('inventory.send_notifications')) {
                $recipientMail = $recipientMail->filter()->unique()->values()->toArray();
                if (count($recipientMail) > 0) {
                    Mail::to($recipientMail)->send(new LowBookInventoryNotification($lowStockBooks, $minQuantity));

                    $this->info('Email thông báo đã được gửi đến ' . count($recipientMail) . ' người nhận.');
                } else {
                    $this->warn('Không tìm thấy người nhận nào để gửi thông báo.');
                }
            }
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Enums\RangeEnum;
use App\Enums\RoleEnum;
use App\Mail\sendEmail;
use App\Models\User;
use App\Services\RentalService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SenddailyStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'book:send-daily-statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $RentalService;
    public function __construct(RentalService $RentalService){
        parent::__construct();
        $this->RentalService = $RentalService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $User=User::query()->where('role',RoleEnum::ADMIN)->get();
        $Data=$this->RentalService->getStatistics(Carbon::today(),RangeEnum::Daily);
        foreach ($User as $item){
            Mail::to($item->email)->send(new sendEmail($Data,"daily"));
        }
    }
}

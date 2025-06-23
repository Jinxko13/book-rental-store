<?php

use App\Jobs\UpdateRentalStatusJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\GenerateMonthlyTopBooksJob;

Schedule::job(\App\Jobs\UpdateRentalStatusJob::class)->everyMinute();

Schedule::command('inventory:check-books')->dailyAt('18:00');

Schedule::command('rental:send-notification --type=all --days=3')
                 ->dailyAt('09:00')
                 ->timezone('Asia/Ho_Chi_Minh')
                 ->onOneServer()
                 ->withoutOverlapping();

Schedule::job(new GenerateMonthlyTopBooksJob)
    ->monthlyOn(1, '01:00')
    ->withoutOverlapping();
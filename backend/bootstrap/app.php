<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\LogRequestMiddleware;
use Illuminate\Console\Scheduling\Schedule;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->append(LogRequestMiddleware::class);
        $middleware->alias([
            'role' => RoleMiddleware::class
        ]);
    })
    ->withSchedule(function (Schedule $schedule){
        $schedule->command('book:send-monthly-statistics')->monthlyOn(1, '00:00');
        $schedule->command('book:send-week-statistics')->weeklyOn(1, '00:00');
        $schedule->command('book:send-daily-statistics')->dailyAt('18:30');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

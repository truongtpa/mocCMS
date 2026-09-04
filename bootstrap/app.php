<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Console\Scheduling\Schedule;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        if(env('APP_ENV') === 'production') {
            $schedule->command('app:create-mail-reminder')->dailyAt('07:00');
            $schedule->command('app:send-email-tb')->everyTenMinutes()->between('7:00', '19:00');;
        }else{
            $schedule->command('app:create-mail-reminder')->everyMinute();
            $schedule->command('app:send-email-tb')->everyMinute();
        }
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [StartSession::class]);
        $middleware->alias([
            'isLogin' => \App\Http\Middleware\isLogin::class,
            'isQuyen' => \App\Http\Middleware\isQuyen::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        if(env('APP_ENV') == 'production'){
            Integration::handles($exceptions);
        }
    })->create();

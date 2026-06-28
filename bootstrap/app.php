<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$bootstrapCachePath = __DIR__.'/cache';

if (! is_dir($bootstrapCachePath) || ! is_writable($bootstrapCachePath)) {
    $tempCachePath = rtrim(sys_get_temp_dir(), "\\/").'/bootstrap/cache';

    if (! is_dir($tempCachePath)) {
        @mkdir($tempCachePath, 0777, true);
    }

    if (is_dir($tempCachePath) && is_writable($tempCachePath)) {
        foreach ([
            'APP_PACKAGES_CACHE' => $tempCachePath.'/packages.php',
            'APP_SERVICES_CACHE' => $tempCachePath.'/services.php',
            'APP_CONFIG_CACHE' => $tempCachePath.'/config.php',
            'APP_ROUTES_CACHE' => $tempCachePath.'/routes-v7.php',
            'APP_EVENTS_CACHE' => $tempCachePath.'/events.php',
        ] as $key => $value) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: ''
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
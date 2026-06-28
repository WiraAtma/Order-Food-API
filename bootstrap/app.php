<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

if (isset($_ENV['VERCEL_JOB_ID']) || isset($_ENV['NOW_REGION'])) {
    $_ENV['APP_BASE_PATH'] = dirname(__DIR__);
    
    $targetCacheDir = '/tmp/storage/bootstrap/cache';
    if (!is_dir($targetCacheDir)) {
        mkdir($targetCacheDir, 0755, true);
    }
    
    putenv("APP_PACKAGES_CACHE={$targetCacheDir}/packages.php");
    putenv("APP_SERVICES_CACHE={$targetCacheDir}/services.php");
    putenv("APP_CONFIG_CACHE={$targetCacheDir}/config.php");
    putenv("APP_ROUTES_CACHE={$targetCacheDir}/routes.php");
}

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: ''
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(HandleCors::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();

if (isset($_ENV['VERCEL_JOB_ID']) || isset($_ENV['NOW_REGION'])) {
    $app->useStoragePath('/tmp/storage');
}

return $app;
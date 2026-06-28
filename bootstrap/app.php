<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

$isVercel = isset($_ENV['VERCEL_JOB_ID']) || isset($_SERVER['VERCEL_URL']) || isset($_ENV['NOW_REGION']);

if ($isVercel) {
    $storagePath = '/tmp/storage';
    $cachePath = '/tmp/storage/bootstrap/cache';

    $directories = [
        $cachePath,
        "{$storagePath}/framework/views",
        "{$storagePath}/framework/cache/data",
        "{$storagePath}/framework/sessions",
        "{$storagePath}/logs",
    ];

    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    $_SERVER['VIEW_COMPILED_PATH'] = "{$storagePath}/framework/views";
    $_ENV['VIEW_COMPILED_PATH'] = "{$storagePath}/framework/views";
    
    $_SERVER['APP_PACKAGES_CACHE'] = "{$cachePath}/packages.php";
    $_SERVER['APP_SERVICES_CACHE'] = "{$cachePath}/services.php";
    $_SERVER['APP_CONFIG_CACHE'] = "{$cachePath}/config.php";
    $_SERVER['APP_ROUTES_CACHE'] = "{$cachePath}/routes.php";
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

if ($isVercel) {
    $app->useStoragePath('/tmp/storage');
    
    $app->resolving('config', function ($config) {
        $config->set('view.compiled', '/tmp/storage/framework/views');
    });
}

return $app;
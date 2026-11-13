<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'super_admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Support for Vercel Serverless ephemeral /tmp storage
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('APP_STORAGE')) {
    $storagePath = getenv('APP_STORAGE') ?: '/tmp/storage';
    $app->useStoragePath($storagePath);
}

return $app;

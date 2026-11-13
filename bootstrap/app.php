<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Ephemeral cache setup for serverless environments (e.g., Vercel / AWS Lambda)
if (is_dir('/tmp') && (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL') || getenv('APP_STORAGE') || !is_writable(__DIR__ . '/cache'))) {
    $cacheDir = '/tmp/bootstrap/cache';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0777, true);
    }
    putenv('APP_PACKAGES_CACHE=' . $cacheDir . '/packages.php');
    putenv('APP_SERVICES_CACHE=' . $cacheDir . '/services.php');
    putenv('APP_CONFIG_CACHE=' . $cacheDir . '/config.php');
    putenv('APP_ROUTES_CACHE=' . $cacheDir . '/routes-v7.php');
    putenv('APP_EVENTS_CACHE=' . $cacheDir . '/events.php');

    $_ENV['APP_PACKAGES_CACHE'] = $cacheDir . '/packages.php';
    $_SERVER['APP_PACKAGES_CACHE'] = $cacheDir . '/packages.php';
    $_ENV['APP_SERVICES_CACHE'] = $cacheDir . '/services.php';
    $_SERVER['APP_SERVICES_CACHE'] = $cacheDir . '/services.php';
    $_ENV['APP_CONFIG_CACHE'] = $cacheDir . '/config.php';
    $_SERVER['APP_CONFIG_CACHE'] = $cacheDir . '/config.php';
    $_ENV['APP_ROUTES_CACHE'] = $cacheDir . '/routes-v7.php';
    $_SERVER['APP_ROUTES_CACHE'] = $cacheDir . '/routes-v7.php';
    $_ENV['APP_EVENTS_CACHE'] = $cacheDir . '/events.php';
    $_SERVER['APP_EVENTS_CACHE'] = $cacheDir . '/events.php';

    foreach (['packages.php', 'services.php'] as $file) {
        $src = __DIR__ . '/cache/' . $file;
        $dst = $cacheDir . '/' . $file;
        if (file_exists($src) && !file_exists($dst)) {
            @copy($src, $dst);
        }
    }
}

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

// Ensure drive letters are recognized as absolute on Windows
if (PHP_OS_FAMILY === 'Windows') {
    $drive = substr(sys_get_temp_dir(), 0, 2);
    if ($drive && $drive[1] === ':') {
        $app->addAbsoluteCachePathPrefix($drive);
    }
}

// Ensure storage path points to writable /tmp on serverless or read-only environments
if (is_dir('/tmp') && (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL') || getenv('APP_STORAGE') || !is_writable(dirname(__DIR__) . '/storage/framework/views'))) {
    $storagePath = getenv('APP_STORAGE') ?: '/tmp/storage';
    $app->useStoragePath($storagePath);
}

return $app;

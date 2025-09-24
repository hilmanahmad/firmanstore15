<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\AuthenticateApi;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
        ]);
        // Tambahkan middleware ke grup 'api'
        $middleware->appendToGroup('api', [
            EnsureFrontendRequestsAreStateful::class, // Memastikan permintaan front-end aman
            ThrottleRequests::class,                // Membatasi jumlah permintaan
            SubstituteBindings::class,              // Mendukung injeksi model binding
            AuthenticateApi::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (NotFoundHttpException $e) {
            return response()->view('error.404', [], 404);
        });
    })->create();

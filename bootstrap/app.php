<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class, 
            'UserAccess' => \App\Http\Middleware\UserAccess::class, 
            'superadmin' => \App\Http\Middleware\IsSuperAdmin::class,
        ]);
        
        // // Updated to include StartSession in the 'web' middleware group
        // $middleware->group('web', [
        //     StartSession::class,
        //     'auth',
        //     SubstituteBindings::class,
        // ]);

        // $middleware->priority([
        //     StartSession::class,
        //     Authenticate::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

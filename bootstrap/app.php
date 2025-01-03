<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Enregistrement des middlewares
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class, // Middleware pour vérifier les droits admin
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class, // Middleware pour gérer l'authentification
            'auth.check' => \App\Http\Middleware\AuthCheckMiddleware::class, // Middleware personnalisé pour afficher un message explicatif
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Configuration des exceptions
    })->create();

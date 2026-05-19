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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'log.csrf' => \App\Http\Middleware\LogCsrfAndSessionErrors::class,
        ]);

        // Ajouter le token CSRF dans tous les headers de réponse pour les requêtes web
        $middleware->web(\App\Http\Middleware\SendCsrfTokenHeader::class);

        // Logger les erreurs CSRF et session pour le débogage
        $middleware->web(\App\Http\Middleware\LogCsrfAndSessionErrors::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

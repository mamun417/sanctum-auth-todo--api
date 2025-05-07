<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function ($exceptions) {
        $exceptions->renderable(function (ValidationException $e) {
            $errors = collect($e->errors())->map(fn($messages) => $messages[0] ?? '');

            return response()->json([
                'message' => $errors->first() . ($errors->count() > 1
                        ? " (and " . ($errors->count() - 1) . " more error" . ($errors->count() > 2 ? 's' : '') . ")"
                        : ''),
                'errors' => $errors
            ], $e->status);
        });
    })->create();

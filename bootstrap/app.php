<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Resource not found.'
                ], Response::HTTP_NOT_FOUND);
            }
        });

        $exceptions->render(function (Throwable $th, Request $request) {
            info(Route::currentRouteName() . ": {$th->getMessage()}");

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Oops! Something went wrong. Please try again later.'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    })->create();

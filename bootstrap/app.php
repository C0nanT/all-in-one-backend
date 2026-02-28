<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn (Request $request, Throwable $e) => $request->is('api/*'));

        $exceptions->dontReport(NotFoundHttpException::class);

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $rawMessage = $e->getMessage();
                $isLaravelModelNotFound = str_contains($rawMessage, 'No query results for model');
                $message = ($rawMessage !== '' && ! $isLaravelModelNotFound)
                    ? $rawMessage
                    : 'Resource not found.';

                return response()->json([
                    'data' => [],
                    'meta' => ['error' => $message],
                ], 404);
            }
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $status = $e->getStatusCode();
                $message = $e->getMessage();

                if ($status >= 500 && ! config('app.debug')) {
                    $message = 'An unexpected error occurred. Please try again later.';
                } elseif ($message === '') {
                    $message = match ($status) {
                        403 => 'You are not allowed to perform this action.',
                        404 => 'Resource not found.',
                        419 => 'The page expired. Please refresh and try again.',
                        500 => 'An unexpected error occurred. Please try again later.',
                        default => 'An error occurred.',
                    };
                }

                return response()->json([
                    'data' => [],
                    'meta' => ['error' => $message],
                ], $status);
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                $meta = config('app.debug')
                    ? [
                        'error' => $e->getMessage(),
                        'exception' => $e::class,
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ]
                    : ['error' => 'An unexpected error occurred. Please try again later.'];

                return response()->json([
                    'data' => [],
                    'meta' => $meta,
                ], 500);
            }
        });
    })->create();

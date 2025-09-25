<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e): Response|JsonResponse
    {
        // Handle API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $e);
        }

        // Handle web requests
        return $this->handleWebException($request, $e);
    }

    /**
     * Report or log an exception.
     */
    public function report(Throwable $e): void
    {
        // Don't report certain exceptions
        if ($this->shouldNotReport($e)) {
            return;
        }

        // Report to external services (Sentry, Bugsnag, etc.)
        if (app()->bound('sentry')) {
            app('sentry')->captureException($e);
        }

        parent::report($e);
    }

    /**
     * Handle API exceptions
     */
    protected function handleApiException(Request $request, Throwable $e): JsonResponse
    {
        // Handle custom API exceptions first
        if ($e instanceof \Modules\Core\Exceptions\ApiException) {
            $this->logException($e, $request);

            return $e->render();
        }

        if ($e instanceof \Modules\Core\Exceptions\BusinessLogicException) {
            $this->logException($e, $request);

            return \Modules\Core\Exceptions\ApiException::badRequest($e->getMessage())->render();
        }

        if ($e instanceof \Modules\Core\Exceptions\ExternalServiceException) {
            $this->logException($e, $request);

            return \Modules\Core\Exceptions\ApiException::serviceUnavailable(
                "External service error: {$e->getService()}"
            )->render();
        }

        $statusCode = $this->getStatusCode($e);
        $errorCode = $this->getErrorCode($e);
        $message = $this->getErrorMessage($e, $statusCode);

        // Log the exception
        $this->logException($e, $request);

        $response = [
            'success' => false,
            'error' => [
                'code' => $errorCode,
                'message' => $message,
                'status_code' => $statusCode,
                'timestamp' => now()->toISOString(),
                'path' => $request->path(),
                'method' => $request->method(),
            ],
            'data' => null,
        ];

        // Add validation errors if applicable
        if ($e instanceof ValidationException) {
            $response['error']['validation_errors'] = $e->errors();
        }

        // Add debug info if in debug mode
        if (config('app.debug') && ! in_array($statusCode, [401, 403, 404, 422, 429])) {
            $response['debug'] = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Handle web exceptions
     */
    protected function handleWebException(Request $request, Throwable $e): Response
    {
        // Log the exception
        $this->logException($e, $request);

        // Handle specific exceptions
        if ($e instanceof ValidationException) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }

        if ($e instanceof AuthenticationException) {
            return redirect()->guest(route('login'));
        }

        if ($e instanceof AuthorizationException) {
            return response()->view('errors.403', [], 403);
        }

        if ($e instanceof ModelNotFoundException) {
            return response()->view('errors.404', [], 404);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->view('errors.404', [], 404);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->view('errors.405', [], 405);
        }

        // Default error handling
        return parent::render($request, $e);
    }

    /**
     * Get HTTP status code for exception
     */
    protected function getStatusCode(Throwable $e): int
    {
        if ($e instanceof ValidationException) {
            return 422;
        }

        if ($e instanceof AuthenticationException) {
            return 401;
        }

        if ($e instanceof AuthorizationException) {
            return 403;
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return 404;
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return 405;
        }

        if ($e instanceof ThrottleRequestsException) {
            return 429;
        }

        if ($e instanceof QueryException) {
            return 500;
        }

        if ($e instanceof HttpException) {
            return $e->getStatusCode();
        }

        return 500;
    }

    /**
     * Get error code for exception
     */
    protected function getErrorCode(Throwable $e): string
    {
        if ($e instanceof ValidationException) {
            return 'VALIDATION_ERROR';
        }

        if ($e instanceof AuthenticationException) {
            return 'UNAUTHENTICATED';
        }

        if ($e instanceof AuthorizationException) {
            return 'UNAUTHORIZED';
        }

        if ($e instanceof ModelNotFoundException) {
            return 'MODEL_NOT_FOUND';
        }

        if ($e instanceof NotFoundHttpException) {
            return 'NOT_FOUND';
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return 'METHOD_NOT_ALLOWED';
        }

        if ($e instanceof ThrottleRequestsException) {
            return 'TOO_MANY_REQUESTS';
        }

        if ($e instanceof QueryException) {
            return 'DATABASE_ERROR';
        }

        if ($e instanceof HttpException) {
            return 'HTTP_ERROR';
        }

        return 'INTERNAL_SERVER_ERROR';
    }

    /**
     * Get user-friendly error message
     */
    protected function getErrorMessage(Throwable $e, int $statusCode): string
    {
        // Don't expose sensitive information in production
        if (config('app.debug')) {
            return $e->getMessage();
        }

        return match ($statusCode) {
            400 => 'Bad Request',
            401 => 'Unauthenticated',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            422 => 'Validation Error',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            default => 'An error occurred',
        };
    }

    /**
     * Log exception with context
     */
    protected function logException(Throwable $e, Request $request): void
    {
        $context = [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'request_data' => $request->except(['password', 'password_confirmation']),
        ];

        if ($e instanceof ValidationException) {
            Log::warning('Validation failed', array_merge($context, [
                'errors' => $e->errors(),
            ]));
        } elseif ($e instanceof AuthenticationException) {
            Log::info('Authentication failed', $context);
        } elseif ($e instanceof AuthorizationException) {
            Log::warning('Authorization failed', $context);
        } elseif ($e instanceof ThrottleRequestsException) {
            Log::warning('Rate limit exceeded', $context);
        } elseif ($e instanceof \Modules\Core\Exceptions\BusinessLogicException) {
            Log::warning('Business logic error', array_merge($context, [
                'business_context' => $e->getContext(),
                'exception' => $e->getMessage(),
            ]));
        } elseif ($e instanceof \Modules\Core\Exceptions\ExternalServiceException) {
            Log::error('External service error', array_merge($context, [
                'service' => $e->getService(),
                'service_context' => $e->getContext(),
                'external_message' => $e->getExternalMessage(),
                'exception' => $e->getMessage(),
            ]));
        } elseif ($e instanceof \Modules\Core\Exceptions\ApiException) {
            Log::error('API exception', array_merge($context, [
                'status_code' => $e->getStatusCode(),
                'errors' => $e->getErrors(),
                'meta' => $e->getMeta(),
                'exception' => $e->getMessage(),
            ]));
        } else {
            Log::error('Exception occurred', array_merge($context, [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]));
        }
    }

    /**
     * Determine if the exception should be reported.
     */
    protected function shouldNotReport(Throwable $e): bool
    {
        // Don't report validation exceptions
        if ($e instanceof ValidationException) {
            return true;
        }

        // Don't report authentication exceptions
        if ($e instanceof AuthenticationException) {
            return true;
        }

        // Don't report authorization exceptions
        if ($e instanceof AuthorizationException) {
            return true;
        }

        // Don't report 404 exceptions
        if ($e instanceof NotFoundHttpException) {
            return true;
        }

        // Don't report method not allowed exceptions
        if ($e instanceof MethodNotAllowedHttpException) {
            return true;
        }

        return false;
    }
}

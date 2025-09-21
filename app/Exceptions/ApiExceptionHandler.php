<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionHandler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
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
    public function render($request, Throwable $e): Response
    {
        // Handle API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions
     */
    protected function handleApiException(Request $request, Throwable $e): JsonResponse
    {
        // Validation exceptions
        if ($e instanceof ValidationException) {
            return $this->validationErrorResponse($e->errors());
        }

        // Model not found exceptions
        if ($e instanceof ModelNotFoundException) {
            $model = class_basename($e->getModel());
            return $this->notFoundResponse("{$model} not found");
        }

        // Authentication exceptions
        if ($e instanceof AuthenticationException) {
            return $this->unauthorizedResponse('Authentication required');
        }

        // HTTP exceptions
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
            
            switch ($statusCode) {
                case 401:
                    return $this->unauthorizedResponse('Unauthorized access');
                case 403:
                    return $this->forbiddenResponse('Access forbidden');
                case 404:
                    return $this->notFoundResponse('Resource not found');
                case 405:
                    return $this->errorResponse('Method not allowed', 405);
                case 422:
                    return $this->validationErrorResponse();
                case 429:
                    return $this->errorResponse('Too many requests', 429);
                case 500:
                    return $this->serverErrorResponse('Internal server error');
                default:
                    return $this->errorResponse($e->getMessage() ?: 'HTTP error', $statusCode);
            }
        }

        // Method not allowed exceptions
        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse('Method not allowed', 405);
        }

        // Not found HTTP exceptions
        if ($e instanceof NotFoundHttpException) {
            return $this->notFoundResponse('Endpoint not found');
        }

        // Custom API exceptions
        if ($e instanceof ApiException) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }

        // Default server error for unhandled exceptions
        $message = config('app.debug') ? $e->getMessage() : 'Internal server error';
        $errors = config('app.debug') ? [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ] : null;

        return $this->serverErrorResponse($message, $errors);
    }
}

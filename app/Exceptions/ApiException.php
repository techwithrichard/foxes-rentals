<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected $statusCode;
    protected $errors;

    public function __construct(
        string $message = 'API Error',
        int $statusCode = 400,
        array $errors = []
    ) {
        parent::__construct($message);
        
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Create a validation exception
     */
    public static function validation(string $message = 'Validation failed', array $errors = []): self
    {
        return new self($message, 422, $errors);
    }

    /**
     * Create a not found exception
     */
    public static function notFound(string $message = 'Resource not found'): self
    {
        return new self($message, 404);
    }

    /**
     * Create an unauthorized exception
     */
    public static function unauthorized(string $message = 'Unauthorized access'): self
    {
        return new self($message, 401);
    }

    /**
     * Create a forbidden exception
     */
    public static function forbidden(string $message = 'Access forbidden'): self
    {
        return new self($message, 403);
    }

    /**
     * Create a server error exception
     */
    public static function serverError(string $message = 'Internal server error'): self
    {
        return new self($message, 500);
    }

    /**
     * Create a conflict exception
     */
    public static function conflict(string $message = 'Resource conflict'): self
    {
        return new self($message, 409);
    }

    /**
     * Create a too many requests exception
     */
    public static function tooManyRequests(string $message = 'Too many requests'): self
    {
        return new self($message, 429);
    }
}

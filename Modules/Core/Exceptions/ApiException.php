<?php

declare(strict_types=1);

namespace Modules\Core\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiException extends Exception
{
    protected int $statusCode;

    protected array $errors;

    protected array $meta;

    public function __construct(
        string $message = 'An error occurred',
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        array $errors = [],
        array $meta = [],
        ?Exception $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
        $this->meta = $meta;
    }

    /**
     * Create a validation exception.
     */
    public static function validation(string $message = 'Validation failed', array $errors = []): self
    {
        return new self($message, Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }

    /**
     * Create a not found exception.
     */
    public static function notFound(string $resource = 'Resource'): self
    {
        return new self("{$resource} not found", Response::HTTP_NOT_FOUND);
    }

    /**
     * Create an unauthorized exception.
     */
    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return new self($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Create a forbidden exception.
     */
    public static function forbidden(string $message = 'Forbidden'): self
    {
        return new self($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Create a conflict exception.
     */
    public static function conflict(string $message = 'Conflict'): self
    {
        return new self($message, Response::HTTP_CONFLICT);
    }

    /**
     * Create a server error exception.
     */
    public static function serverError(string $message = 'Internal server error', ?Exception $previous = null): self
    {
        return new self($message, Response::HTTP_INTERNAL_SERVER_ERROR, [], [], $previous);
    }

    /**
     * Create a bad request exception.
     */
    public static function badRequest(string $message = 'Bad request'): self
    {
        return new self($message, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Create a rate limit exceeded exception.
     */
    public static function rateLimitExceeded(string $message = 'Rate limit exceeded'): self
    {
        return new self($message, Response::HTTP_TOO_MANY_REQUESTS);
    }

    /**
     * Create a service unavailable exception.
     */
    public static function serviceUnavailable(string $message = 'Service unavailable'): self
    {
        return new self($message, Response::HTTP_SERVICE_UNAVAILABLE);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $this->getMessage(),
            'status_code' => $this->statusCode,
        ];

        if ($this->errors !== []) {
            $response['errors'] = $this->errors;
        }

        if ($this->meta !== []) {
            $response['meta'] = $this->meta;
        }

        if (config('app.debug') && $this->getPrevious()) {
            $response['debug'] = [
                'exception' => get_class($this->getPrevious()),
                'file' => $this->getPrevious()->getFile(),
                'line' => $this->getPrevious()->getLine(),
                'trace' => $this->getPrevious()->getTraceAsString(),
            ];
        }

        return response()->json($response, $this->statusCode);
    }

    /**
     * Get the status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the errors.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get the meta data.
     */
    public function getMeta(): array
    {
        return $this->meta;
    }
}

<?php

declare(strict_types=1);

namespace Modules\Core\Exceptions;

use Exception;

class ExternalServiceException extends Exception
{
    protected string $service;

    protected array $context;

    protected ?string $externalMessage;

    public function __construct(
        string $service,
        string $message = '',
        array $context = [],
        ?string $externalMessage = null,
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message ?: "External service error: {$service}", $code, $previous);
        $this->service = $service;
        $this->context = $context;
        $this->externalMessage = $externalMessage;
    }

    /**
     * Create a service timeout exception.
     */
    public static function timeout(string $service, array $context = []): self
    {
        return new self($service, "Service {$service} timed out", $context);
    }

    /**
     * Create a service unavailable exception.
     */
    public static function unavailable(string $service, array $context = []): self
    {
        return new self($service, "Service {$service} is unavailable", $context);
    }

    /**
     * Create an authentication failure exception.
     */
    public static function authenticationFailed(string $service, array $context = []): self
    {
        return new self($service, "Authentication failed for service {$service}", $context);
    }

    /**
     * Create a rate limit exceeded exception.
     */
    public static function rateLimitExceeded(string $service, array $context = []): self
    {
        return new self($service, "Rate limit exceeded for service {$service}", $context);
    }

    /**
     * Create an invalid response exception.
     */
    public static function invalidResponse(string $service, string $response, array $context = []): self
    {
        return new self($service, "Invalid response from service {$service}", $context, $response);
    }

    /**
     * Create a connection failed exception.
     */
    public static function connectionFailed(string $service, array $context = []): self
    {
        return new self($service, "Connection failed to service {$service}", $context);
    }

    /**
     * Create a payment processing exception.
     */
    public static function paymentFailed(string $service, string $reason, array $context = []): self
    {
        return new self($service, "Payment processing failed: {$reason}", $context);
    }

    /**
     * Create an email delivery exception.
     */
    public static function emailDeliveryFailed(string $service, string $reason, array $context = []): self
    {
        return new self($service, "Email delivery failed: {$reason}", $context);
    }

    /**
     * Create a file upload exception.
     */
    public static function fileUploadFailed(string $service, string $reason, array $context = []): self
    {
        return new self($service, "File upload failed: {$reason}", $context);
    }

    /**
     * Create an API quota exceeded exception.
     */
    public static function quotaExceeded(string $service, array $context = []): self
    {
        return new self($service, "API quota exceeded for service {$service}", $context);
    }

    /**
     * Get the service name.
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * Get the context data.
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get the external service error message.
     */
    public function getExternalMessage(): ?string
    {
        return $this->externalMessage;
    }
}

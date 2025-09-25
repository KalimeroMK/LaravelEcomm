<?php

declare(strict_types=1);

namespace Modules\Core\Exceptions;

use Exception;

class BusinessLogicException extends Exception
{
    protected array $context;

    public function __construct(string $message = '', array $context = [], int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * Create an invalid operation exception.
     */
    public static function invalidOperation(string $operation, array $context = []): self
    {
        return new self("Invalid operation: {$operation}", $context);
    }

    /**
     * Create an invalid state exception.
     */
    public static function invalidState(string $state, array $context = []): self
    {
        return new self("Invalid state: {$state}", $context);
    }

    /**
     * Create an insufficient permissions exception.
     */
    public static function insufficientPermissions(string $action, array $context = []): self
    {
        return new self("Insufficient permissions for action: {$action}", $context);
    }

    /**
     * Create a resource limit exceeded exception.
     */
    public static function resourceLimitExceeded(string $resource, int $limit, array $context = []): self
    {
        return new self("Resource limit exceeded for {$resource}. Limit: {$limit}", $context);
    }

    /**
     * Create a duplicate resource exception.
     */
    public static function duplicateResource(string $resource, string $identifier, array $context = []): self
    {
        return new self("Duplicate {$resource} with identifier: {$identifier}", $context);
    }

    /**
     * Create a dependency conflict exception.
     */
    public static function dependencyConflict(string $resource, string $dependency, array $context = []): self
    {
        return new self("Cannot modify {$resource} due to dependency on {$dependency}", $context);
    }

    /**
     * Create an expired resource exception.
     */
    public static function expiredResource(string $resource, array $context = []): self
    {
        return new self("Resource {$resource} has expired", $context);
    }

    /**
     * Create an inactive resource exception.
     */
    public static function inactiveResource(string $resource, array $context = []): self
    {
        return new self("Resource {$resource} is inactive", $context);
    }

    /**
     * Get the context data.
     */
    public function getContext(): array
    {
        return $this->context;
    }
}

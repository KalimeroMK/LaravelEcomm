<?php

declare(strict_types=1);

namespace Modules\Core\Service;

use Throwable;

abstract class CoreService
{
    /**
     * Get the service name.
     */
    final public function getServiceName(): string
    {
        return class_basename(static::class);
    }

    /**
     * Get the module name.
     */
    final public function getModuleName(): string
    {
        $namespace = get_class($this);
        $parts = explode('\\', $namespace);

        return $parts[1] ?? 'Unknown';
    }

    /**
     * Log service activity.
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        $context = array_merge($context, [
            'service' => $this->getServiceName(),
            'module' => $this->getModuleName(),
        ]);

        logger()->{$level}($message, $context);
    }

    /**
     * Log info message.
     */
    protected function logInfo(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    /**
     * Log warning message.
     */
    protected function logWarning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    /**
     * Log error message.
     */
    protected function logError(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    /**
     * Handle service exceptions.
     */
    protected function handleException(Throwable $e, string $operation = 'unknown'): void
    {
        $this->logError("Exception in {$operation}: {$e->getMessage()}", [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'operation' => $operation,
        ]);
    }
}

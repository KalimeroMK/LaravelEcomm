<?php

declare(strict_types=1);

namespace Modules\Core\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class GeneralException extends Exception
{
    /**
     * Additional data related to the exception.
     *
     * @var array<string, mixed>
     */
    public array $data = [];

    /**
     * @var int
     */
    protected $code = 500;

    /**
     * @var string
     */
    protected $message = 'Internal system error';

    protected bool $log = true;
    /**
     * @param  Exception|null  $exception
     * @param  array  $data
     */

    /**
     * The exception to log.
     */
    protected ?Exception $exception = null;

    /**
     * Initializes a new instance of the GeneralException class.
     *
     * @param  Exception|null  $exception  The related exception, if any.
     * @param  array<string, mixed>  $data  Additional data about the exception.
     */
    public function __construct(?Exception $exception = null, array $data = [])
    {
        $this->setException($exception);
        $this->setData($data);
        parent::__construct($this->message());
    }

    public function message(): ?string
    {
        return $this->message;
    }

    public function getException(): null
    {
        return $this->exception;
    }

    /**
     * Set the exception.
     */
    public function setException(?Exception $exception): void
    {
        $this->exception = $exception;
    }

    /**
     * Retrieves the additional data related to the exception.
     *
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Sets the additional data related to the exception.
     *
     * @param  array<string, mixed>  $data  Additional data about the exception.
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function render(): JsonResponse
    {
        if ($this->isLog()) {
            $this->renderLog();
        }

        return $this->prepareResponse();
    }

    public function isLog(): bool
    {
        return $this->log;
    }

    /**
     * Log error
     */
    public function renderLog(): void
    {
        Log::error(print_r($this->getLogResponse(), true));
    }

    /**
     * Returns a structured array for logging purposes.
     *
     * @return array<string, mixed>
     */
    public function getLogResponse(): array
    {
        return [
            'message' => $this->getLogMessage(),
            'code' => $this->getCode(),
            'line' => $this->line(),
            'file' => $this->file(),
        ];
    }

    /**
     * Returns the log message; defaults to an empty string if no exception is set.
     */
    public function getLogMessage(): string
    {
        return $this->exception instanceof Exception ? $this->exception->getMessage() : '';
    }

    /**
     * Returns the exception line number or 'none'.
     */
    public function line(): int|string
    {
        return $this->exception instanceof Exception ? $this->exception->getLine() : 'none';
    }

    /**
     * Returns the file in which the exception was thrown or 'none'.
     */
    public function file(): int|string
    {
        return $this->exception instanceof Exception ? $this->exception->getFile() : 'none';
    }

    /**
     * Returns a structured array to form the basis of a response.
     *
     * @return array<string, mixed>
     */
    public function getResponse(): array
    {
        return [
            'code' => $this->getCode(),
            'message' => $this->message(),
        ];
    }

    /**
     * Handle an ajax response.
     */
    protected function prepareResponse(): JsonResponse
    {
        return response()->json($this->getResponse());
    }
}

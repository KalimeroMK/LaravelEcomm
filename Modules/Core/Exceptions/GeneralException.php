<?php

namespace Modules\Core\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class GeneralException extends Exception
{
    /**
     * Additional data related to the exception.
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
    /**
     * @var bool
     */
    protected bool $log = true;
    /**
     * @param  Exception|null  $exception
     * @param  array  $data
     */

    /**
     * The exception to log.
     * @var Exception|null
     */
    protected ?Exception $exception = null;

    /**
     * Initializes a new instance of the GeneralException class.
     * @param  Exception|null  $exception  The related exception, if any.
     * @param  array<string, mixed>  $data  Additional data about the exception.
     */
    public function __construct(?Exception $exception = null, array $data = [])
    {
        $this->setException($exception);
        $this->setData($data);
        parent::__construct($this->message());
    }

    /**
     * @return string|null
     */
    public function message(): ?string
    {
        return $this->message;
    }

    /**
     * @return null
     */
    public function getException(): null
    {
        return $this->exception;
    }

    /**
     * Set the exception.
     * @param  Exception|null  $exception
     */
    public function setException(?Exception $exception): void
    {
        $this->exception = $exception;
    }

    /**
     * Retrieves the additional data related to the exception.
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Sets the additional data related to the exception.
     * @param  array<string, mixed>  $data  Additional data about the exception.
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }


    /**
     * @param  string  $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function render(): JsonResponse
    {
        $this->isLog() ? $this->renderLog() : null;

        return $this->prepareResponse();
    }

    /**
     * @return bool
     */
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
     * @return string
     */
    public function getLogMessage(): string
    {
        return $this->exception ? $this->exception->getMessage() : '';
    }

    /**
     * Returns the exception line number or 'none'.
     * @return int|string
     */
    public function line(): int|string
    {
        return $this->exception ? $this->exception->getLine() : 'none';
    }

    /**
     * Returns the file in which the exception was thrown or 'none'.
     * @return int|string
     */
    public function file(): int|string
    {
        return $this->exception ? $this->exception->getFile() : 'none';
    }

    /**
     * Handle an ajax response.
     */
    protected function prepareResponse(): JsonResponse
    {
        return response()->json($this->getResponse());
    }

    /**
     * Returns a structured array to form the basis of a response.
     * @return array<string, mixed>
     */
    public function getResponse(): array
    {
        return [
            'code' => $this->getCode(),
            'message' => $this->message(),
        ];
    }

}

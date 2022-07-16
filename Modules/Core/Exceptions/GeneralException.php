<?php

namespace App\Modules\Core\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class GeneralException extends Exception
{
    /**
     * Any extra data to send with the response.
     *
     * @var array
     */
    public array $data = [];
    
    protected $code = 500;
    
    protected $message = 'Internal system error';
    
    protected string $logMessage = 'Internal system error';
    
    protected bool $log = true;
    
    protected $exception = null;
    
    /**
     * GeneralException constructor.
     *
     * @param  Exception|null  $exception
     * @param  array  $data
     */
    public function __construct(?Exception $exception = null, $data = [])
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
    public function getException()
    {
        return $this->exception;
    }
    
    /**
     * @param  null  $exception
     */
    public function setException($exception): void
    {
        $this->exception = $exception;
    }
    
    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
    
    /**
     * Set the extra data to send with the response.
     *
     * @param  array  $data
     *
     * @return $this
     */
    public function setData(array $data): GeneralException
    {
        $this->data = $data;
        
        return $this;
    }
    
    /**
     * @param  int  $code
     */
    public function setCode(int $code)
    {
        $this->code = $code;
    }
    
    /**
     * @param  string  $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }
    
    public function render($request): JsonResponse
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
     * @param  bool  $log
     */
    public function setLog(bool $log): void
    {
        $this->log = $log;
    }
    
    /**
     * Log error
     */
    public function renderLog()
    {
        Log::error(print_r($this->getLogResponse(), true));
    }
    
    /**
     * @return array
     */
    public function getLogResponse(): array
    {
        return [
            'message' => $this->getLogMessage(),
            'code'    => $this->getCode(),
            'line'    => $this->line(),
            'file'    => $this->file(),
        ];
    }
    
    /**
     * @return string
     */
    public function getLogMessage(): string
    {
        return $this->exception ? $this->exception->getMessage() : '';
    }
    
    /**
     * @param  string  $logMessage
     */
    public function setLogMessage(string $logMessage): void
    {
        $this->logMessage = $logMessage;
    }
    
    /**
     * @return int
     */
    public function line()
    {
        return $this->exception ? $this->exception->getLine() : 'none';
    }
    
    /**
     * @return int
     */
    public function file()
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
     * @return array
     */
    public function getResponse(): array
    {
        return [
            'code'    => $this->getCode(),
            'message' => $this->message(),
        ];
    }
    
}

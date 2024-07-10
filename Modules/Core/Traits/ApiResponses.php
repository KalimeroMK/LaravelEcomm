<?php

namespace Modules\Core\Traits;

use Exception;
use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    public int $responseCode = 200;

    public string $message = 'OK';

    public string $title = 'Success';

    /**
     * @return $this
     */
    public function setCode(int $code = 200): static
    {
        $this->responseCode = $code;

        return $this;
    }

    /**
     * Set the message property.
     *
     * @return $this
     */
    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set the title property.
     *
     * @return $this
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function respond(mixed $data): JsonResponse
    {
        return response()
            ->json(
                [
                    'message' => $this->message,
                    'code' => $this->responseCode,
                    'data' => $data,
                ],
                $this->responseCode
            );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function exceptionRespond(Exception $exception, array $data = [], string $title = 'Error'): JsonResponse
    {
        return response()->json([
            'title' => $title,
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'data' => $data,
        ], $exception->getCode());
    }

    public function respondWithExceptionError(Exception $exception, string $title = 'Error'): JsonResponse
    {
        return response()
            ->json(
                [
                    'title' => $title,
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                ],
                $exception->getCode()
            );
    }

    protected function errorResponse(string $message, int $code): JsonResponse
    {
        return response()->json(['message' => $message, 'code' => $code], $code);
    }
}

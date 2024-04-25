<?php

namespace Modules\Core\Traits;

use Exception;
use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    /**
     * @var int
     */
    public int $responseCode = 200;

    /**
     * @var string
     */
    public string $message = 'OK';

    /**
     * @var string
     */
    public string $title = 'Success';

    /**
     * @param  int  $code
     *
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
     * @param  string  $message
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
     * @param  string  $title
     *
     * @return $this
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param $data
     *
     * @return JsonResponse
     */
    public function respond($data): JsonResponse
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
     * @param  Exception  $exception
     * @param  array  $data
     * @param  string  $title
     *
     * @return JsonResponse
     */
    public function exceptionRespond(Exception $exception, array $data = [], string $title = 'Error'): JsonResponse
    {
        return response()->json([
            'title' => $title,
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ], $exception->getCode());
    }

    /**
     * @param  Exception  $exception
     * @param  string  $title
     *
     * @return JsonResponse
     */
    public function respondWithExceptionError(Exception $exception, string $title = 'Error'): JsonResponse
    {
        return response()
            ->json(
                [
                    'title' => $this->title,
                    'message' => $this->message,
                ],
                $exception->getCode()
            );
    }

    /**
     * @param $message
     * @param $code
     *
     * @return JsonResponse
     */
    protected function errorResponse($message, $code): JsonResponse
    {
        return response()->json(['message' => $message, 'code' => $code], $code);
    }

}

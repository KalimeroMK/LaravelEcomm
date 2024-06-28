<?php

namespace Modules\Core\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Core\Traits\ApiResponses;

class CoreController extends Controller
{
    use ApiResponses;

    /**
     * Send a JSON response back to the client.
     *
     * @param  array<mixed>  $result  The result data to send back. Expected keys and types:
     *                              - 'items': array<Item>
     *                              - 'count': int
     *                              - 'totalPrice': float
     *                              - etc., depending on what $result can include
     * @param  string  $message  The message to include in the response.
     *
     * @return JsonResponse
     */
    public function sendResponse(array $result, string $message): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response);
    }


    /**
     * Return an error response.
     *
     * @param  string  $error  The main error message.
     * @param  array<string>  $errorMessages  Additional error messages or details, typically strings.
     * @param  int  $code  HTTP status code, defaults to 404.
     *
     * @return JsonResponse
     */
    public function sendError(string $error, array $errorMessages = [], int $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
            'errors' => $errorMessages,
        ];

        return response()->json($response, $code);
    }


}
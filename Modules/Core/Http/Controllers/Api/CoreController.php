<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Modules\Core\Traits\ApiResponses;

class CoreController extends Controller
{
    use ApiResponses;

    /**
     * Send a JSON response back to the client.
     *
     * @param  array  $result  The result data to send back. Expected keys and types:
     *                         - 'items': array<Item>
     *                         - 'count': int
     *                         - 'totalPrice': float
     *                         - etc., depending on what $result can include
     * @param  string  $message  The message to include in the response.
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

    /**
     * Authorize an action for a given model ID fetched via repository.
     *
     * @template TModel of Model
     *
     * @param  class-string  $repoClass  The repository class name
     * @param  string  $ability  The name of the policy ability (e.g. 'view', 'update')
     * @param  int  $id  The model ID
     * @return TModel
     */
    protected function authorizeFromRepo(string $repoClass, string $ability, int $id): mixed
    {
        /** @var object{findById: callable} $repo */
        $repo = app($repoClass);
        $model = $repo->findById($id);

        $this->authorize($ability, $model);

        return $model;
    }
}

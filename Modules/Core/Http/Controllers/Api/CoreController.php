<?php

namespace Modules\Core\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Core\Traits\ApiResponses;

class CoreController extends Controller
{
    use ApiResponses;
    
    /**
     * @param $result
     * @param $message
     *
     * @return JsonResponse
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        
        return response()->json($response, 200);
    }
    
    /**
     * return error response.
     *
     * @return JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];
        
        if ( ! empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }
        
        return response()->json($response, $code);
    }
}
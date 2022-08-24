<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Modules\Core\Traits\ApiResponses;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
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

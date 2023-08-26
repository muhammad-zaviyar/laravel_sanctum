<?php

use Illuminate\Http\JsonResponse;

// if(!function_exists('success')){

//     function success($data, $message = null, $code = 200): JsonResponse
//     {
//         return response()->json([
//             'status' => 'Request was successfull',
//             'message' => $message,
//             'data' => $data
//         ], $code);
//     }
// }

// if(!function_exists('error')){

//     function error($data, $message = null, $code): JsonResponse
//     {
//         return response()->json([
//             'status' => 'Error has occured..',
//             'message' => $message,
//             'data' => $data
//         ], $code);
//     }
// }

if (!function_exists('apiResponse')) {

    function apiResponse($data, $status, $message, $code = 200): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}

?>

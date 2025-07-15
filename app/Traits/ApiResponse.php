<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponse
{
    /**
     * @param array $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function successResponse(
        mixed $data = [],
        string $message = 'success',
        int $code = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * @param array $errors
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function errorResponse(
        array $errors = [],
        string $message = 'Something went wrong!, kindly try again',
        int $code = Response::HTTP_BAD_REQUEST
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}

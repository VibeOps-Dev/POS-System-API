<?php

namespace App\Traits;

use App\Http\HttpStatus;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Returns a JSON response with a success status.
     * 
     * @param int $code HTTP status code.
     * @param string $message success message.
     * @param mixed $data data to be returned.
     * @param mixed $rest additional data to be returned.
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse(
        int $code = HttpStatus::OK,
        string $message = '',
        $data = null,
        $rest = []
    ): JsonResponse 
    {
        return response()->json([
            'status' => 'success',
            'status_code' => $code,
            'message' => $message,
            ...($data === null ? [] : ['data' => $data]),
            ...$rest,
        ], $code);
    }

    /**
     * Returns a JSON response with an error status.
     * 
     * @param int $code HTTP status code.
     * @param string $message error message.
     * @param mixed $errors data to be returned.
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse(
        int $code = HttpStatus::BAD_REQUEST,
        string $message = '',
        $errors = null
    ): JsonResponse 
    {
        return response()->json([
            'status' => 'error',
            'status_code' => $code,
            'message' => $message,
            ...($errors === null ? [] : ['errors' => $errors]),
        ], $code);
    }
}

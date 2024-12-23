<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    /**
     * Generate success type response.
     *
     * Returns the success data and message if there is any error
     *
     * @param  object  $data
     * @param  string  $message
     * @param  int  $status_code
     */
    public function responseSuccess($data, $message = 'Successful', $status_code = JsonResponse::HTTP_OK): JsonResponse
    {
        return response()->json([
            'status' => $status_code,
            'message' => $message,
            'errors' => null,
            'data' => $data,
        ], $status_code);
    }

    /**
     * Generate Error response.
     *
     * Returns the errors data if there is any error
     *
     * @param  object  $errors
     */
    public function responseError($errors, $message = 'Data is invalid', $status_code = JsonResponse::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'status' => $status_code,
            'message' => $message,
            'errors' => $errors,
            'data' => null,
        ], $status_code);
    }
}

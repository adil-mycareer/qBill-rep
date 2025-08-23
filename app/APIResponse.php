<?php

namespace App;

use Illuminate\Http\Response;

trait APIResponse
{

    protected function successResponse($data = [], $message = "Success", $statusCode = Response::HTTP_OK)
    {
        return response()->json([
            "status" => true,
            "message" => $message,
            "data" => $data,
        ], $statusCode);
    }

    protected function errorResponse($message = "Oops! Something went wrong on our side. Please refresh the page or contact support if this continues.", $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return response()->json([
            "status" => false,
            "message" => $message,
        ], $statusCode);
    }
}

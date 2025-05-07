<?php

namespace App\Traits;

use App\Constants\HttpStatusCodes;
use Illuminate\Http\Resources\Json\JsonResource;

trait ResponseHelperTrait
{
    public static function success($message, $data = [], $statusCode = HttpStatusCodes::OK)
    {
        if ($data instanceof JsonResource) {
            $data = $data->response()->getData(true);
        }

        return response()->json(array_merge(['success' => true, 'message' => $message], $data), $statusCode);
    }

    public static function error($message, $statusCode = HttpStatusCodes::BAD_REQUEST)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }
}

<?php

namespace App\Helpers;
use Illuminate\Http\Response;

class ResponseHelper{
    public static function Response(bool $state, string $message, int $statusCode = Response::HTTP_OK, $data = null, $property = 'data'){
        if(!is_null($data) && $property == 'code'){
            return response()->json(['success' => $state, $property => $data, 'message' => $message], $statusCode);
        }
        
        if(!is_null($data)){
            return response()->json(['success' => $state, 'message' => $message, $property => $data], $statusCode);
        }

        return response()->json(['success' => $state,'message' => $message], $statusCode);
    }
}
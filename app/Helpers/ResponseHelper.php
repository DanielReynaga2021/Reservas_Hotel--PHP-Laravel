<?php

namespace App\Helpers;

class ResponseHelper{
    public static function Response(bool $state, string $message, $data = null, $property = 'data'){
        if(!is_null($data)){
            return response()->json(['success' => $state,'message' => $message, $property => $data]);
        }
        return response()->json(['success' => $state,'message' => $message]);
    }
}
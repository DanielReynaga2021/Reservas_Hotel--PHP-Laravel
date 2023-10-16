<?php

namespace App\Helpers;

class ResponseHelper{
    public static function Response(bool $state, string $message, $property = 'data', $data = false){
        if($data){
            return response()->json(['success' => $state,'message' => $message, $property => $data]);
        }
        return response()->json(['success' => $state,'message' => $message]);
    }
}
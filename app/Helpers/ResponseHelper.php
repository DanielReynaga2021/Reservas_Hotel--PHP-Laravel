<?php

namespace App\Helpers;

class ResponseHelper{
    public static function Response(bool $state, string $message, $data = true){
        if($data){
            return response()->json(['success' => $state,'message' => $message,'data' => $data]);
        }
        return response()->json(['success' => $state,'message' => $message]);
    }
}
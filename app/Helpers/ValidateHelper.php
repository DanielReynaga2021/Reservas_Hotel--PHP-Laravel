<?php

namespace App\Helpers;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class ValidateHelper{
    public static function validateWebService($response, $message){
        if ($response->failed()) {
            throw new HttpResponseException(
                ResponseHelper::Response(false, 'Internal Server Error', Response::HTTP_SERVICE_UNAVAILABLE));
        }
        
        if (empty($response->object()->data)) {
            throw new HttpResponseException(
                ResponseHelper::Response(false, $message, Response::HTTP_SERVICE_UNAVAILABLE));
        }
    }
}

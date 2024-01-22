<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DbException extends Exception
{
    public function __construct(
        $message,
        $code,
        $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render(Request $request){
        
        if($request->isJson()){
            return ResponseHelper::Response(false, $this->message, $this->code, 'DB_CONNECTION_FAILED', 'code');
        }
    }
}

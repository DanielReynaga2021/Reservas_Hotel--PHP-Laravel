<?php

namespace App\Http\Services;
use App\Helpers\ResponseHelper;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AuthService{

    public function registerUser(AuthRequest $authRequest){
        $user = new User();
        $user->name = $authRequest->name;
        $user->email = $authRequest->email;
        $user->password = $authRequest->password;
        $user->save();

        return ResponseHelper::Response(true, 'successfully registered');
    }

    public function loginUser(Array $credentials){
        if(!Auth::attempt($credentials)){
            return response()->json([
                'success'=> false,
                'message'=> "Unauthorized",
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $user = User::Where("email", $credentials["email"])->first();
        $token = $user->createToken("auth_token")->plainTextToken;
        
        return ResponseHelper::Response(true, 'started session with success', 'token', $token);
    }
}
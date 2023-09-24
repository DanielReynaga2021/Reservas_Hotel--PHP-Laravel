<?php

namespace App\Http\Services;
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

        return response()->json([
            'success'=> true,
        ]);
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
        
        return response()->json([
            'success'=> true,
            'message'=> "ok",
            'token'=> $token,
        ], JsonResponse::HTTP_ACCEPTED);
    }
}
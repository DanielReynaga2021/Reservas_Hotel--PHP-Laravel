<?php

namespace App\Http\Services;

use App\Helpers\ResponseHelper;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class UserService{

    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository,
    )
    {
        $this->userRepository = $userRepository;
    }
    public function registerUser(UserRequest $userRequest){
        $user = $this->buildUser($userRequest);
        $this->userRepository->createUser($user);
        return ResponseHelper::Response(true, 'successfully registered');
    }

    public function loginUser(Array $credentials){
        if(!Auth::attempt($credentials)){
           return ResponseHelper::Response(false, "authorization error for {$credentials["email"]}", JsonResponse::HTTP_UNAUTHORIZED, 'UNAUTHORIZED_LOGIN', 'code');
        }

        $user = $this->userRepository->getUserByEmail($credentials["email"]);
        $token = $user->createToken("auth_token")->plainTextToken;
        
        return ResponseHelper::Response(true, 'started session with success', Response::HTTP_OK, $token, 'token');
    }

    public function logoutUser(){
        auth()->user()->tokens()->delete();
        return ResponseHelper::Response(true, 'logged out successfully');
    }

    public function buildUser(UserRequest $userRequest){
        $user = new User();
        $user->name = $userRequest->name;
        $user->email = $userRequest->email;
        $user->password = $userRequest->password;
        return $user;
    }
}
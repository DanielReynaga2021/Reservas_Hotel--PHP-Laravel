<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(
        UserService $userService,
    )
    {
        $this->userService = $userService;
    }
    public function registerUser(UserRequest $userRequest){
        return $this->userService->registerUser($userRequest);
    }

    public function loginUser(Request $request){
        $credentials =$request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        
       return $this->userService->loginUser($credentials);
    }

    public function logoutUser(){
        return $this->userService->logoutUser();
    }
}

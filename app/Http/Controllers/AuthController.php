<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(
        AuthService $authService,
    )
    {
        $this->authService = $authService;
    }
    public function registerUser(AuthRequest $authRequest){
        return $this->authService->registerUser($authRequest);
    }

    public function loginUser(Request $request){
        $credentials =$request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        
       return $this->authService->loginUser($credentials);
    }
}
